import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:laundry_customer/models/schedule_model.dart';
import 'package:laundry_customer/models/schedules_model/schedule.dart';
import 'package:laundry_customer/models/vendor_appointments.dart';
import 'package:laundry_customer/providers/cart_provider.dart';
import 'package:laundry_customer/providers/misc_providers.dart';
import 'package:laundry_customer/repos/vendor_appointments_repo.dart';
import 'package:laundry_customer/widgets/time_slot_tile.dart';

/// Only this checkout's vendor is consulted; city and delivery address are unrelated.
class VendorAppointmentPicker extends ConsumerStatefulWidget {
  const VendorAppointmentPicker({super.key, required this.legacyPicker});
  final Widget legacyPicker;
  @override
  ConsumerState<VendorAppointmentPicker> createState() =>
      _VendorAppointmentPickerState();
}

class _VendorAppointmentPickerState
    extends ConsumerState<VendorAppointmentPicker> {
  VendorAppointments? _data;
  AppointmentSlot? _pickup, _delivery;
  String? _vendor, _error;
  bool _loading = true;
  int _request = 0;

  @override
  void initState() {
    super.initState();
    Future.microtask(() => _changeVendor(ref.read(activeVendorIdProvider)));
  }

  void _publish(String type, AppointmentSlot? slot) {
    ref.read(scheduleProvider(type).notifier).state = slot == null
        ? null
        : ScheduleModel(
            schedule: Schedule(hour: slot.hour, title: slot.title),
            dateTime: DateTime.parse(slot.date),
            vendorId: _vendor);
    ref.read(dateProvider(type).notifier).state =
        slot == null ? null : DateTime.parse(slot.date);
  }

  void _changeVendor(String? vendor) {
    if (!mounted) return;
    _vendor = vendor;
    _pickup = _delivery = null;
    _publish('Pick Up', null);
    _publish('Delivery', null);
    _load();
  }

  Future<void> _load() async {
    final request = ++_request;
    if (_vendor == null || _vendor!.isEmpty) {
      setState(() {
        _data = const VendorAppointments(
            enabled: false, pickup: [], delivery: [], processingMinutes: 0);
        _loading = false;
      });
      return;
    }
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final data = await ref
          .read(vendorAppointmentsRepoProvider)
          .load(_vendor!, pickup: _pickup);
      if (!mounted || request != _request) return;
      setState(() {
        _data = data;
        _loading = false;
        // Availability can change after another customer confirms a booking.
        if (_pickup != null &&
            !data.pickup.any((s) => s.key == _pickup!.key && s.available)) {
          _pickup = _delivery = null;
          _publish('Pick Up', null);
          _publish('Delivery', null);
        }
        if (_pickup != null) {
          final available = data.delivery.where((s) => s.available).toList();
          _delivery =
              available.where((s) => s.key == _delivery?.key).firstOrNull ??
                  available.firstOrNull;
          _publish('Delivery', _delivery);
        }
      });
    } catch (_) {
      if (!mounted || request != _request) return;
      setState(() {
        _loading = false;
        _error =
            'تعذر تحديث المواعيد. تحقق من الاتصال وأعد اختيار موعد الاستلام.';
        _pickup = _delivery = null;
      });
      _publish('Pick Up', null);
      _publish('Delivery', null);
    }
  }

  @override
  Widget build(BuildContext context) {
    ref.listen(activeVendorIdProvider, (previous, next) {
      if (next != _vendor) _changeVendor(next);
    });
    if (_loading)
      return const Padding(
          padding: EdgeInsets.all(24),
          child: Center(child: CircularProgressIndicator()));
    if (_error != null)
      return Column(children: [
        Text(_error!),
        TextButton(onPressed: _load, child: const Text('إعادة المحاولة'))
      ]);
    final data = _data;
    if (data == null || !data.enabled) return widget.legacyPicker;
    return Column(crossAxisAlignment: CrossAxisAlignment.stretch, children: [
      const Text('مواعيد المتجر · توقيت فلسطين',
          style: TextStyle(fontWeight: FontWeight.bold)),
      const SizedBox(height: 12),
      _SlotCards(
          title: 'استلام الغسيل منك',
          slots: data.pickup,
          selected: _pickup,
          onSelect: (slot) {
            setState(() {
              _pickup = slot;
              _delivery = null;
            });
            _publish('Pick Up', slot);
            _publish('Delivery', null);
            _load();
          }),
      if (_pickup != null) ...[
        const Divider(height: 30),
        Text(
            'مدة التجهيز: ${data.processingMinutes ~/ 60} ساعة و${data.processingMinutes % 60} دقيقة من نهاية فترة الاستلام'),
        _SlotCards(
            title: 'إعادة الغسيل إليك',
            slots: data.delivery,
            selected: _delivery,
            onSelect: (slot) {
              setState(() => _delivery = slot);
              _publish('Delivery', slot);
            }),
      ],
      if (_pickup != null && _delivery != null)
        Container(
            margin: const EdgeInsets.only(top: 16),
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
                color: const Color(0xffeffaf8),
                borderRadius: BorderRadius.circular(14)),
            child: Text(
                'ملخص الحجز\nالاستلام: ${_pickup!.date} · ${_pickup!.title}\nالتوصيل: ${_delivery!.date} · ${_delivery!.title}\nيُثبت التوفر عند تأكيد الطلب.',
                style: const TextStyle(height: 1.8))),
      TextButton.icon(
          onPressed: _load,
          icon: const Icon(Icons.refresh),
          label: const Text('تحديث التوفر')),
    ]);
  }
}

class _SlotCards extends StatefulWidget {
  const _SlotCards(
      {required this.title,
      required this.slots,
      required this.selected,
      required this.onSelect});
  final String title;
  final List<AppointmentSlot> slots;
  final AppointmentSlot? selected;
  final ValueChanged<AppointmentSlot> onSelect;
  @override
  State<_SlotCards> createState() => _SlotCardsState();
}

class _SlotCardsState extends State<_SlotCards> {
  String? _day;

  @override
  Widget build(BuildContext context) {
    final days = widget.slots.map((s) => s.date).toSet().toList();
    final selectedDay = days.contains(_day)
        ? _day
        : widget.selected?.date ??
            widget.slots.where((s) => s.available).firstOrNull?.date ??
            days.firstOrNull;
    if (days.isEmpty) {
      return Padding(
          padding: const EdgeInsets.symmetric(vertical: 12),
          child: Text(
              '${widget.title}: لا توجد مواعيد متاحة. اختر فترة استلام أخرى أو تواصل مع المتجر.'));
    }
    final locale = Localizations.localeOf(context).languageCode;
    final daySlots =
        widget.slots.where((s) => s.date == selectedDay).toList();
    final availableCount = daySlots.where((s) => s.available).length;
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      const SizedBox(height: 10),
      Row(children: [
        Expanded(
          child: Text(widget.title,
              style: const TextStyle(
                  fontWeight: FontWeight.w800, fontSize: 14)),
        ),
        Container(
          padding:
              const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
          decoration: BoxDecoration(
            color: const Color(0xFFEAF6F1),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: TimeSlotTile.teal.withOpacity(0.25)),
          ),
          child: Text('الأوقات المتاحة: $availableCount',
              style: const TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFF0F766E))),
        ),
      ]),
      const SizedBox(height: 6),
      const TimeSlotLegend(),
      const SizedBox(height: 10),
      SizedBox(
          height: 78,
          child: ListView.separated(
              scrollDirection: Axis.horizontal,
              itemCount: days.length,
              separatorBuilder: (_, __) => const SizedBox(width: 8),
              itemBuilder: (context, index) {
                final date = days[index];
                final isSelected = date == selectedDay;
                final daySlotsAll =
                    widget.slots.where((s) => s.date == date).toList();
                final dayAvailable = daySlotsAll.any((s) => s.available);
                final dayAvailableCount =
                    daySlotsAll.where((s) => s.available).length;
                return InkWell(
                  borderRadius: BorderRadius.circular(16),
                  onTap: () => setState(() => _day = date),
                  child: Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                    decoration: BoxDecoration(
                      gradient: isSelected
                          ? const LinearGradient(
                              colors: [TimeSlotTile.teal, TimeSlotTile.tealMid],
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                            )
                          : null,
                      color: isSelected
                          ? null
                          : (dayAvailable
                              ? Colors.white
                              : TimeSlotTile.bookedBg),
                      borderRadius: BorderRadius.circular(16),
                      border: isSelected
                          ? null
                          : Border.all(
                              color: dayAvailable
                                  ? TimeSlotTile.teal.withOpacity(0.35)
                                  : TimeSlotTile.bookedBorder,
                              width: 1.2,
                            ),
                      boxShadow: [
                        BoxShadow(
                          color: isSelected
                              ? TimeSlotTile.teal.withOpacity(0.35)
                              : const Color(0xFF0B1E2E).withOpacity(0.05),
                          blurRadius: isSelected ? 12 : 8,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Text(
                          DateFormat('EEE d/M', locale)
                              .format(DateTime.parse(date)),
                          style: TextStyle(
                            fontSize: 12.5,
                            fontWeight: FontWeight.w900,
                            color: isSelected
                                ? Colors.white
                                : (dayAvailable
                                    ? const Color(0xFF0F2E35)
                                    : TimeSlotTile.bookedInk),
                            decoration: dayAvailable
                                ? null
                                : TextDecoration.lineThrough,
                            decorationThickness: 1.4,
                            fontFamily: 'Cairo',
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          dayAvailable ? '$dayAvailableCount متاح' : 'محجوز',
                          style: TextStyle(
                            fontSize: 9.5,
                            fontWeight: FontWeight.w800,
                            color: isSelected
                                ? Colors.white.withOpacity(0.92)
                                : (dayAvailable
                                    ? TimeSlotTile.teal
                                    : TimeSlotTile.bookedCaption),
                            fontFamily: 'Cairo',
                          ),
                        ),
                      ],
                    ),
                  ),
                );
              })),
      const SizedBox(height: 10),
      Wrap(
          spacing: 8,
          runSpacing: 8,
          children: daySlots
              .map((slot) => SizedBox(
                  width: 158,
                  child: TimeSlotTile(
                    title: slot.title,
                    available: slot.available,
                    selected: widget.selected?.key == slot.key,
                    reason: slot.reason,
                    onTap: slot.available ? () => widget.onSelect(slot) : null,
                  )))
              .toList()),
    ]);
  }
}
