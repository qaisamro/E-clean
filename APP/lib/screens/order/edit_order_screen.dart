import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_screenutil/flutter_screenutil.dart';
import 'package:gap/gap.dart';
import 'package:intl/intl.dart';
import 'package:laundry_customer/models/cart_item_model.dart';
import 'package:laundry_customer/models/order_details_model/order.dart';
import 'package:laundry_customer/models/order_place_model/order_place_model.dart'
    show OrderProductModel;
import 'package:laundry_customer/models/products_model/product.dart';
import 'package:laundry_customer/providers/address_book_provider.dart';
import 'package:laundry_customer/providers/order_providers.dart';
import 'package:laundry_customer/providers/vendor_providers.dart';
import 'package:laundry_customer/repos/order_repo.dart';
import 'package:laundry_customer/services/network_exceptions.dart';
import 'package:laundry_customer/widgets/app_network_image.dart';

/// Full order edit screen — available while the order is still pending
/// confirmation. Edits products (quantities / remove / add from the same
/// store), delivery address, date and hour, plus free-text notes.
class EditOrderScreen extends ConsumerStatefulWidget {
  const EditOrderScreen({super.key, required this.orderID});
  final String orderID;

  @override
  ConsumerState<EditOrderScreen> createState() => _EditOrderScreenState();
}

class _LineItem {
  _LineItem({
    required this.productId,
    required this.name,
    required this.unitPrice,
    required this.qty,
    this.image,
  });

  final int productId;
  final String name;
  final double unitPrice;
  int qty;
  final String? image;
}

class _EditOrderScreenState extends ConsumerState<EditOrderScreen> {
  final List<_LineItem> _items = [];
  bool _initialized = false;
  bool _saving = false;

  final TextEditingController _instructionCtrl = TextEditingController();
  String? _selectedAddressId;
  DateTime _selectedDate = DateTime.now();
  TimeOfDay _selectedTime = TimeOfDay.now();

  @override
  void dispose() {
    _instructionCtrl.dispose();
    super.dispose();
  }

  void _initFromOrder(Order order) {
    if (_initialized) return;
    _items
      ..clear()
      ..addAll((order.products ?? []).map((p) {
        final qtyFromModel = order.quantity?.quantity.firstWhere(
          (q) => q.product_id == p.id.toString(),
          orElse: () => CartItemModel(
            product_id: p.id.toString(),
            quantity: 1,
          ),
        );
        return _LineItem(
          productId: p.id ?? -1,
          name: p.name ?? '',
          unitPrice: (p.currentPrice ?? 0).toDouble(),
          qty: qtyFromModel?.quantity ?? 1,
          image: p.imagePath,
        );
      }));
    _instructionCtrl.text = order.instruction ?? '';
    final addrId = order.address?.id?.toString();
    _selectedAddressId = addrId != null && addrId.isNotEmpty ? addrId : null;
    try {
      if (order.deliveryDate != null && order.deliveryDate!.isNotEmpty) {
        _selectedDate = DateTime.parse(order.deliveryDate!);
      }
    } catch (_) {}
    try {
      final raw = order.deliveryHourRaw ?? '';
      if (raw.isNotEmpty) {
        final parts = raw.split(':');
        _selectedTime =
            TimeOfDay(hour: int.parse(parts[0]), minute: int.parse(parts[1]));
      }
    } catch (_) {}
    _initialized = true;
  }

  double get _subtotal =>
      _items.fold<double>(0, (sum, e) => sum + e.unitPrice * e.qty);

  void _bumpQty(int productId, int delta) {
    setState(() {
      final index = _items.indexWhere((e) => e.productId == productId);
      if (index < 0) return;
      final item = _items[index];
      final next = item.qty + delta;
      if (next <= 0) {
        _items.removeAt(index);
      } else {
        item.qty = next;
      }
    });
  }

  void _removeItem(int productId) {
    setState(() => _items.removeWhere((e) => e.productId == productId));
  }

  Future<void> _save() async {
    if (_saving) return;
    if (_items.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('يجب أن يبقى صنف واحد على الأقل في الطلب')),
      );
      return;
    }
    setState(() => _saving = true);
    final repo = ref.read(orderRepoProvider);
    try {
      // Delivery details first (no side effects on products).
      await repo.editOrderDelivery(
        orderID: widget.orderID,
        instruction:
            _instructionCtrl.text.trim().isEmpty ? null : _instructionCtrl.text.trim(),
        deliveryDate: DateFormat('yyyy-MM-dd', 'en').format(_selectedDate),
        deliveryHour: '${_selectedTime.hour.toString().padLeft(2, '0')}:00',
        addressId: int.tryParse(_selectedAddressId ?? ''),
      );

      // Products (server re-prices everything from the catalog).
      final products = _items
          .map((e) => OrderProductModel(
              id: e.productId.toString(),
              quantity: e.qty.toString()))
          .toList();
      await repo.updateOrder(products, widget.orderID);

      ref.invalidate(orderDetailsProvider(widget.orderID));
      if (!mounted) return;
      Navigator.of(context).pop();
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('تم تحديث الطلب بنجاح')),
      );
    } catch (e) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(NetworkExceptions.errorText(e))),
      );
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  Future<void> _pickDate() async {
    final date = await showDatePicker(
      context: context,
      initialDate: _selectedDate,
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (date != null) setState(() => _selectedDate = date);
  }

  Future<void> _pickTime() async {
    final time = await showTimePicker(
      context: context,
      initialTime: _selectedTime,
    );
    if (time != null) setState(() => _selectedTime = time);
  }

  void _showAddProductSheet(Order order) {
    final initialQty = _items.fold<int>(0, (sum, e) => sum + e.qty);
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (sheetContext) => _AddProductSheet(
        order: order,
        initialSelectionQty: initialQty,
        onAdd: (product, qty) {
          setState(() {
            final id = product.id ?? -1;
            final existing =
                _items.indexWhere((e) => e.productId == id);
            if (existing >= 0) {
              _items[existing].qty += qty;
            } else {
              _items.add(_LineItem(
                productId: id,
                name: product.name ?? '',
                unitPrice: (product.currentPrice ?? 0).toDouble(),
                qty: qty,
                image: product.imagePath,
              ));
            }
          });
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final async = ref.watch(orderDetailsProvider(widget.orderID));

    return Scaffold(
      backgroundColor: const Color(0xFFFAF7F2),
      appBar: AppBar(
        backgroundColor: const Color(0xFF028090),
        elevation: 0,
        leading: InkWell(
          onTap: () => Navigator.of(context).pop(),
          borderRadius: BorderRadius.circular(12),
          child: const Padding(
            padding: EdgeInsets.all(8),
            child: Icon(Icons.arrow_forward_rounded, color: Colors.white),
          ),
        ),
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'تعديل الطلب',
              style: TextStyle(
                  fontFamily: 'Cairo',
                  fontSize: 15.sp,
                  fontWeight: FontWeight.w900,
                  color: Colors.white),
            ),
            Text(
              'متاح حتى تأكيد المتجر للطلب',
              style: TextStyle(
                  fontFamily: 'Cairo',
                  fontSize: 10.5.sp,
                  fontWeight: FontWeight.w600,
                  color: Colors.white.withOpacity(0.9)),
            ),
          ],
        ),
        bottom: const PreferredSize(
          preferredSize: Size.fromHeight(4),
          child: SizedBox(height: 4, child: ColoredBox(color: Color(0xFF00A896))),
        ),
      ),
      body: async.maybeWhen(
        loaded: (details) {
          final order = details.data?.order;
          if (order == null) {
            return const Center(child: Text('تعذر تحميل الطلب'));
          }
          _initFromOrder(order);
          return _buildBody(context, order);
        },
        loading: () => const Center(child: CircularProgressIndicator(
          color: Color(0xFF028090),
        )),
        error: (e) => Center(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Text(e,
                textAlign: TextAlign.center,
                style: TextStyle(
                    fontFamily: 'Cairo',
                    fontSize: 13.sp,
                    color: const Color(0xFFE63946))),
          ),
        ),
        orElse: () => const Center(child: CircularProgressIndicator(
          color: Color(0xFF028090),
        )),
      ),
      bottomNavigationBar: _buildSaveBar(context),
    );
  }

  Widget _buildBody(BuildContext context, Order order) {
    return SingleChildScrollView(
      padding: EdgeInsets.fromLTRB(16.w, 16.h, 16.w, 24.h),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          _sectionTitle('الأصناف'),
          Container(
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: const Color(0xFFE2E8F0)),
            ),
            padding: EdgeInsets.all(12.w),
            child: Column(
              children: [
                if (_items.isEmpty)
                  const Padding(
                    padding: EdgeInsets.symmetric(vertical: 20),
                    child: Text('لا توجد أصناف',
                        style: TextStyle(
                            fontFamily: 'Cairo',
                            color: Color(0xFF94A3B8))),
                  ),
                ..._items.asMap().entries.map(
                  (entry) => _itemRow(entry.value),
                ),
                const Divider(height: 18, color: Color(0xFFF1F5F9)),
                InkWell(
                  borderRadius: BorderRadius.circular(14.r),
                  onTap: () => _showAddProductSheet(order),
                  child: Container(
                    height: 46.h,
                    alignment: Alignment.center,
                    decoration: BoxDecoration(
                      color: const Color(0xFFEFFAF8),
                      borderRadius: BorderRadius.circular(14.r),
                      border: Border.all(
                          color: const Color(0xFF00A896).withOpacity(0.4)),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: const [
                        Icon(Icons.add_circle_outline_rounded,
                            size: 18, color: Color(0xFF028090)),
                        Gap(8),
                        Text('إضافة صنف من المتجر',
                            style: TextStyle(
                                fontFamily: 'Cairo',
                                fontWeight: FontWeight.w800,
                                fontSize: 13,
                                color: Color(0xFF028090))),
                      ],
                    ),
                  ),
                ),
              ],
            ),
          ),
          const Gap(18),
          _sectionTitle('التوصيل'),
          _card(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                _label('العنوان'),
                const Gap(6),
                _addressDropdown(),
                const Gap(12),
                Row(
                  children: [
                    Expanded(
                      child: _pickerTile(
                        icon: Icons.calendar_today_rounded,
                        label: DateFormat('yyyy-MM-dd', 'en')
                            .format(_selectedDate),
                        onTap: _pickDate,
                      ),
                    ),
                    const Gap(10),
                    Expanded(
                      child: _pickerTile(
                        icon: Icons.schedule_rounded,
                        label: _selectedTime.format(context),
                        onTap: _pickTime,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
          const Gap(18),
          _sectionTitle('ملاحظات'),
          _card(
            child: TextField(
              controller: _instructionCtrl,
              maxLines: 3,
              style: const TextStyle(
                fontFamily: 'Cairo',
                fontSize: 13,
                color: Color(0xFF0B1E2E),
              ),
              decoration: InputDecoration(
                hintText: 'اكتب أي ملاحظة للتوصيل...',
                hintStyle: TextStyle(
                  fontFamily: 'Cairo',
                  fontSize: 12,
                  color: Colors.grey.shade500,
                ),
                filled: true,
                fillColor: const Color(0xFFF8FAFC),
                contentPadding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(14.r),
                  borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                ),
              ),
            ),
          ),
          const Gap(18),
          _summaryCard(),
          const Gap(24),
        ],
      ),
    );
  }

  Widget _sectionTitle(String text) => Padding(
        padding: const EdgeInsets.only(bottom: 8),
        child: Text(
          text,
          style: TextStyle(
            fontFamily: 'Cairo',
            fontSize: 12.5.sp,
            fontWeight: FontWeight.w900,
            color: const Color(0xFF64748B),
          ),
        ),
      );

  Widget _label(String text) => Text(
        text,
        style: TextStyle(
          fontFamily: 'Cairo',
          fontSize: 11.5.sp,
          fontWeight: FontWeight.w800,
          color: const Color(0xFF1E293B),
        ),
      );

  Widget _card({required Widget child}) => Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: const Color(0xFFE2E8F0)),
        ),
        padding: EdgeInsets.all(14.w),
        child: child,
      );

  Widget _itemRow(_LineItem item) => Padding(
        padding: EdgeInsets.symmetric(vertical: 8.h),
        child: Row(
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(12),
              child: SizedBox(
                width: 54.w,
                height: 54.w,
                child: AppNetworkImage(
                  item.image ?? '',
                  fit: BoxFit.cover,
                  placeholder: 'assets/images/app_icon.png',
                ),
              ),
            ),
            const Gap(12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    item.name,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: TextStyle(
                      fontFamily: 'Cairo',
                      fontSize: 12.sp,
                      fontWeight: FontWeight.w800,
                      color: const Color(0xFF0B1E2E),
                    ),
                  ),
                  Text(
                    '₪ ${item.unitPrice.toStringAsFixed(2)} / الوحدة',
                    style: TextStyle(
                      fontFamily: 'Cairo',
                      fontSize: 11.sp,
                      color: const Color(0xFF64748B),
                    ),
                  ),
                ],
              ),
            ),
            Row(
              children: [
                _stepper(Icons.remove_rounded,
                    () => _bumpQty(item.productId, -1)),
                const Gap(6),
                SizedBox(
                  width: 24,
                  child: Text(
                    '${item.qty}',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontFamily: 'Cairo',
                      fontSize: 13.sp,
                      fontWeight: FontWeight.w900,
                      color: const Color(0xFF0B1E2E),
                    ),
                  ),
                ),
                const Gap(6),
                _stepper(Icons.add_rounded,
                    () => _bumpQty(item.productId, 1)),
              ],
            ),
            const Gap(8),
            InkWell(
              onTap: () => _removeItem(item.productId),
              borderRadius: BorderRadius.circular(8),
              child: const Padding(
                padding: EdgeInsets.all(6),
                child:
                    Icon(Icons.delete_outline_rounded, size: 20, color: Color(0xFFE63946)),
              ),
            ),
          ],
        ),
      );

  Widget _stepper(IconData icon, VoidCallback onTap) => Material(
        color: const Color(0xFFEFFAF8),
        shape: const CircleBorder(),
        child: InkWell(
          customBorder: const CircleBorder(),
          onTap: onTap,
          child: SizedBox(
            width: 28.h,
            height: 34.h,
            child: Icon(icon, size: 16, color: const Color(0xFF028090)),
          ),
        ),
      );

  Widget _addressDropdown() {
    final book = ref.watch(addressBookProvider);
    if (book.addresses.isEmpty) {
      return const Text(
        'لا توجد عناوين محفوظة — أضفها من صفحة العناوين',
        style: TextStyle(
          fontFamily: 'Cairo',
          fontSize: 12,
          color: Color(0xFF94A3B8),
        ),
      );
    }
    final currentExists = book.addresses
        .any((a) => a.id?.toString() == _selectedAddressId);
    return DropdownButtonFormField<String>(
      value: currentExists ? _selectedAddressId : null,
      hint: const Text('اختر عنواناً',
          style: TextStyle(fontFamily: 'Cairo', fontSize: 13)),
      decoration: InputDecoration(
        filled: true,
        fillColor: const Color(0xFFF8FAFC),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14.r),
          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
        ),
        contentPadding:
            const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      ),
      items: book.addresses
          .map(
            (a) => DropdownMenuItem<String>(
              value: a.id?.toString(),
              child: Text(
                a.addressLine?.toString() ??
                    a.addressName?.toString() ??
                    'عنوان #${a.id}',
                style: const TextStyle(fontFamily: 'Cairo', fontSize: 13),
                overflow: TextOverflow.ellipsis,
              ),
            ),
          )
          .toList(),
      onChanged: (v) => setState(() => _selectedAddressId = v),
    );
  }

  Widget _pickerTile({
    required IconData icon,
    required String label,
    required VoidCallback onTap,
  }) {
    return Material(
      color: const Color(0xFFF8FAFC),
      borderRadius: BorderRadius.circular(14),
      child: InkWell(
        borderRadius: BorderRadius.circular(14),
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: const Color(0xFFE2E8F0)),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(icon, size: 17, color: const Color(0xFF028090)),
              const Gap(8),
              Text(
                label,
                style: const TextStyle(
                  fontFamily: 'Cairo',
                  fontWeight: FontWeight.w800,
                  fontSize: 13,
                  color: Color(0xFF0B1E2E),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _summaryCard() => Container(
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            colors: [Color(0xFF028090), Color(0xFF00A896)],
            begin: Alignment.topRight,
            end: Alignment.bottomLeft,
          ),
          borderRadius: BorderRadius.circular(20),
          boxShadow: const [
            BoxShadow(
              color: Color(0x5502474A),
              blurRadius: 16,
              offset: Offset(0, 6),
            ),
          ],
        ),
        padding: EdgeInsets.all(16.w),
        child: Column(
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('المجموع الفرعي',
                    style: TextStyle(
                        fontFamily: 'Cairo',
                        color: Colors.white,
                        fontSize: 12,
                        fontWeight: FontWeight.w700)),
                Text('₪ ${_subtotal.toStringAsFixed(2)}',
                    style: const TextStyle(
                        fontFamily: 'Cairo',
                        color: Colors.white,
                        fontSize: 13,
                        fontWeight: FontWeight.w800)),
              ],
            ),
            const Gap(10),
            const Text(
              'التوصيل والخصم والإجمالي يُحسبون من الخادم عند الحفظ',
              style: TextStyle(
                  fontFamily: 'Cairo',
                  color: Colors.white70,
                  fontSize: 10,
                  fontWeight: FontWeight.w600),
              textAlign: TextAlign.center,
            ),
          ],
        ),
      );

  Widget _buildSaveBar(BuildContext context) => Container(
        decoration: BoxDecoration(
          color: Colors.white,
          border: const Border(top: BorderSide(color: Color(0xFFE2E8F0))),
        ),
        padding: EdgeInsets.fromLTRB(16.w, 12.h, 16.w, 16.h),
        child: SafeArea(
          top: false,
          child: SizedBox(
            width: double.infinity,
            height: 54.h,
            child: ElevatedButton(
              onPressed: _saving ? null : _save,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF028090),
                disabledBackgroundColor:
                    const Color(0xFF028090).withValues(alpha: 0.6),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16.r),
                ),
                elevation: 0,
              ),
              child: _saving
                  ? const SizedBox(
                      width: 22,
                      height: 22,
                      child: CircularProgressIndicator(
                        strokeWidth: 2.4,
                        color: Colors.white,
                      ),
                    )
                  : const Text(
                      'حفظ التعديلات',
                      style: TextStyle(
                        fontFamily: 'Cairo',
                        fontWeight: FontWeight.w900,
                        fontSize: 16,
                        color: Colors.white,
                      ),
                    ),
            ),
          ),
        ),
      );
}

/// Searchable catalog picker — adds products from the same store.
class _AddProductSheet extends ConsumerStatefulWidget {
  const _AddProductSheet({
    required this.order,
    required this.onAdd,
    required this.initialSelectionQty,
  });
  final Order order;
  final void Function(Product product, int qty) onAdd;
  final int initialSelectionQty;

  @override
  ConsumerState<_AddProductSheet> createState() => _AddProductSheetState();
}

class _AddProductSheetState extends ConsumerState<_AddProductSheet> {
  final TextEditingController _searchCtrl = TextEditingController();
  String _query = '';
  int _sessionAddedCount = 0;
  final Map<int, int> _sessionQty = {};

  @override
  void dispose() {
    _searchCtrl.dispose();
    super.dispose();
  }

  void _addAndReport(Product product) {
    final id = product.id ?? -1;
    widget.onAdd(product, 1);
    setState(() {
      _sessionAddedCount += 1;
      _sessionQty[id] = (_sessionQty[id] ?? 0) + 1;
    });
  }

  @override
  Widget build(BuildContext context) {
    final vendorId = widget.order.vendorId?.toString() ?? '';
    if (vendorId.isEmpty) {
      return Container(
        height: 200,
        alignment: Alignment.center,
        color: Colors.white,
        child: const Text('تعذر تحديد متجر هذا الطلب',
            style: TextStyle(fontFamily: 'Cairo')),
      );
    }
    final filter = VendorProductFilter(
      vendorId: vendorId,
      serviceId: 'all',
      variantId: '',
    );
    final async = ref.watch(vendorProductsProvider(filter));

    return Padding(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
      ),
      child: Container(
        height: MediaQuery.of(context).size.height * 0.85,
        decoration: const BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.vertical(top: Radius.circular(26)),
        ),
        child: Column(
          children: [
            const SizedBox(height: 12),
            const SizedBox(
              width: 46,
              height: 5,
              child: DecoratedBox(
                decoration: BoxDecoration(
                  color: Color(0xFFE2E8F0),
                  borderRadius: BorderRadius.all(Radius.circular(3)),
                ),
              ),
            ),
            const Gap(12),
            Text(
              'إضافة صنف من متجر الطلب',
              style: TextStyle(
                fontFamily: 'Cairo',
                fontWeight: FontWeight.w900,
                fontSize: 15.sp,
                color: const Color(0xFF0B1E2E),
              ),
            ),
            const Gap(10),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: TextField(
                controller: _searchCtrl,
                onChanged: (v) => setState(() => _query = v.trim()),
                style: const TextStyle(
                    fontFamily: 'Cairo', fontSize: 13),
                decoration: InputDecoration(
                  hintText: 'ابحث عن صنف...',
                  hintStyle: const TextStyle(
                      fontFamily: 'Cairo', fontSize: 12),
                  prefixIcon:
                      const Icon(Icons.search_rounded, color: Color(0xFF028090)),
                  filled: true,
                  fillColor: const Color(0xFFF8FAFC),
                  contentPadding: const EdgeInsets.symmetric(vertical: 12),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(14),
                    borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                  ),
                ),
              ),
            ),
            const Gap(8),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: AnimatedSwitcher(
                duration: const Duration(milliseconds: 200),
                child: _sessionAddedCount == 0
                    ? const SizedBox(height: 2, key: ValueKey('banner-empty'))
                    : Container(
                        key: ValueKey(_sessionAddedCount),
                        width: double.infinity,
                        padding: const EdgeInsets.symmetric(
                            horizontal: 12, vertical: 9),
                        decoration: BoxDecoration(
                          color: const Color(0xFFE7F8F5),
                          border: Border.all(
                              color: const Color(0xFF00A896),
                              width: 1.2),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Row(
                          children: [
                            const Icon(Icons.check_circle_rounded,
                                size: 18, color: Color(0xFF00A896)),
                            const Gap(8),
                            Expanded(
                              child: Text(
                                'أُضيفت $_sessionAddedCount صنف — إجمالي قطع الطلب: ${widget.initialSelectionQty + _sessionAddedCount}',
                                style: const TextStyle(
                                  fontFamily: 'Cairo',
                                  fontWeight: FontWeight.w800,
                                  fontSize: 12,
                                  color: Color(0xFF0F766E),
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
              ),
            ),
            const Gap(4),
            Expanded(
              child: async.maybeWhen(
                loaded: (model) {
                  final products =
                      (model.data?.products ?? []).where((p) {
                    if (_query.isEmpty) return true;
                    return (p.name ?? '')
                        .toLowerCase()
                        .contains(_query.toLowerCase());
                  }).toList();
                  if (products.isEmpty) {
                    return const Center(
                      child: Text('لا توجد نتائج',
                          style: TextStyle(
                              fontFamily: 'Cairo',
                              color: Color(0xFF94A3B8))),
                    );
                  }
                  return GridView.builder(
                    padding: const EdgeInsets.fromLTRB(14, 4, 14, 20),
                    gridDelegate:
                        const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      childAspectRatio: 0.78,
                      crossAxisSpacing: 10,
                      mainAxisSpacing: 10,
                    ),
                    itemCount: products.length,
                    itemBuilder: (context, index) {
                      final p = products[index];
                      return _PickerCard(
                        product: p,
                        count: _sessionQty[p.id ?? -1] ?? 0,
                        onAdd: () => _addAndReport(p),
                      );
                    },
                  );
                },
                loading: () => const Center(
                  child: CircularProgressIndicator(color: Color(0xFF028090)),
                ),
                error: (e) => Center(
                  child: Padding(
                    padding: const EdgeInsets.all(24),
                    child: Text(e,
                        textAlign: TextAlign.center,
                        style: const TextStyle(
                            fontFamily: 'Cairo', fontSize: 12)),
                  ),
                ),
                orElse: () => const Center(
                  child: CircularProgressIndicator(color: Color(0xFF028090)),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _PickerCard extends StatelessWidget {
  const _PickerCard({required this.product, required this.onAdd, this.count = 0});
  final Product product;
  final VoidCallback onAdd;
  final int count;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.white,
      borderRadius: BorderRadius.circular(18),
      child: InkWell(
        borderRadius: BorderRadius.circular(18),
        onTap: onAdd,
        child: Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(18),
            border: Border.all(color: const Color(0xFFE2E8F0)),
            boxShadow: [
              BoxShadow(
                color: const Color(0xFF0B1E2E).withValues(alpha: 0.04),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          padding: const EdgeInsets.all(10),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Expanded(
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: Stack(
                    fit: StackFit.expand,
                    children: [
                      AppNetworkImage(
                        product.imagePath ?? '',
                        fit: BoxFit.cover,
                        placeholder: 'assets/images/app_icon.png',
                      ),
                      if (count > 0)
                        Positioned(
                          top: 8,
                          right: 8,
                          child: Container(
                            padding: const EdgeInsets.symmetric(
                                horizontal: 9, vertical: 5),
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                colors: [Color(0xFF028090), Color(0xFF00A896)],
                              ),
                              borderRadius: BorderRadius.circular(20),
                              boxShadow: const [
                                BoxShadow(
                                  color: Color(0x4402474A),
                                  blurRadius: 6,
                                  offset: Offset(0, 2),
                                ),
                              ],
                            ),
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                const Icon(Icons.check_rounded,
                                    size: 12, color: Colors.white),
                                const Gap(3),
                                Text('أُضيفت $count',
                                    style: const TextStyle(
                                      fontFamily: 'Cairo',
                                      fontWeight: FontWeight.w800,
                                      fontSize: 11,
                                      color: Colors.white,
                                    )),
                              ],
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
              ),
              const Gap(8),
              Text(
                product.name ?? '',
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(
                    fontFamily: 'Cairo',
                    fontSize: 12,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF0B1E2E),
                    height: 1.3),
              ),
              const Gap(4),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Text(
                    '₪ ${(product.currentPrice ?? 0).toStringAsFixed(2)}',
                    style: const TextStyle(
                      fontFamily: 'Cairo',
                      fontSize: 13,
                      fontWeight: FontWeight.w900,
                      color: Color(0xFF028090),
                    ),
                  ),
                  Container(
                    width: 34,
                    height: 34,
                    decoration: const BoxDecoration(
                      gradient: LinearGradient(
                        colors: [Color(0xFF028090), Color(0xFF00A896)],
                        begin: Alignment.topRight,
                        end: Alignment.bottomLeft,
                      ),
                      shape: BoxShape.circle,
                    ),
                    child: const Icon(Icons.add_rounded,
                        size: 18, color: Colors.white),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
