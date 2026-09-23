@extends('website.layouts.app')

@section('content')
    @auth
        <!-- breadcrumb -->
        <section
            class="rs-breadcrumb-area bg-[#1A7058] h-[260px] w-full bg-[url('{{ asset('website/assets/images/header/breadcrumb.png') }}')] bg-cover bg-center flex flex-col items-center justify-center text-center">
            <div class="rs-breadcrumb-content">
                <h1
                    class="rs-breadcrumb-title mb-[5px] sm:mb-[10px] text-[26px] md:text-[30px] md:text-4xl text-white font-semibold leading-[140%]">
                    {{ __('My Addresses') }}
                </h1>
                <div class="rs-breadcrumb-top-content">
                    <a href="{{ route('web.settings') }}"
                        class="text-base md:text-lg text-white font-normal leading-[100%]">{{ __('Dashboard') }}
                        / </a>
                    <a href="{{ route('web.addresses') }}"
                        class="text-base md:text-lg text-white font-normal leading-[100%]">{{ __('Addresses') }}</a>
                </div>
            </div>
        </section>

        <!-- Add Address Modal -->
        <div id="addAddressModal"
            class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md my-auto">
                <div class="flex justify-between items-center mb-[30px]">
                    <h3 class="text-neutral-700 text-lg font-semibold">{{ __('Add New Address') }}</h3>
                    <button onclick="toggleModal('addAddressModal')"
                        class="transition-transform duration-300 hover:rotate-90 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('web.addresses.store') }}" method="POST"
                    class="overflow-y-auto max-h-[calc(100vh-200px)]">
                    @csrf
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Label') }}</label>
                        <input type="text" name="address_name" placeholder="{{ __('Example') }}: {{ __('Home') }}, {{ __('Office') }}..."
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('House No') }}</label>
                        <input type="text" name="house_no" placeholder="123"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Flat No') }}</label>
                        <input type="text" name="flat_no" placeholder="1A"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Road No') }}</label>
                        <input type="text" name="road_no" placeholder="Main Road"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Block') }}</label>
                        <input type="text" name="block" placeholder="{{ __('Block') }}"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Area') }}</label>
                        <input type="text" name="area" placeholder="{{ __('Area') }}" required
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Address Line') }}</label>
                        <input type="text" name="address_line" placeholder="123 Main Road, Apt 6B"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Post Code') }}</label>
                        <input type="text" name="post_code" placeholder="10003"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-[30px]">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Phone Number') }}</label>
                        <input type="text" name="phone" placeholder="+1 (555) 545-5421"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <button type="submit"
                        class="rs-add-new-address-btn text-sm bg-linear-to-r from-cyan-500 to-blue-500 text-white h-[48px] text-center leading-[48px] w-[100%] rounded-xl">
                        {{ __('Save Address') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- {{ __('Edit') }} Address Modal -->
        <div id="editAddressModal"
            class="hidden fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-2xl p-6 w-full max-w-md my-auto">
                <div class="flex justify-between items-center mb-[30px]">
                    <h3 class="text-neutral-700 text-lg font-semibold">{{ __('Edit') }} {{ __('Address') }}</h3>
                    <button onclick="toggleModal('editAddressModal')"
                        class="transition-transform duration-300 hover:rotate-90 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form id="editForm" method="POST" class="overflow-y-auto max-h-[calc(100vh-200px)]">
                    @csrf
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Label') }}</label>
                        <input type="text" name="address_name" id="edit_address_name"
                            placeholder="{{ __('Example') }}: {{ __('Home') }}, {{ __('Office') }}..."
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('House No') }}</label>
                        <input type="text" name="house_no" id="edit_house_no" placeholder="123"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Flat No') }}</label>
                        <input type="text" name="flat_no" id="edit_flat_no" placeholder="1A"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Road No') }}</label>
                        <input type="text" name="road_no" id="edit_road_no" placeholder="Main Road"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Block') }}</label>
                        <input type="text" name="block" id="edit_block" placeholder="{{ __('Block') }}"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Area') }}</label>
                        <input type="text" name="area" id="edit_area" placeholder="{{ __('Area') }}"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Address Line') }}</label>
                        <input type="text" name="address_line" id="edit_address_line" placeholder="123 Main Road, Apt 6B"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-4">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Post Code') }}</label>
                        <input type="text" name="post_code" id="edit_post_code" placeholder="10003"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="mb-[30px]">
                        <label class="text-neutral-700 text-base font-medium">{{ __('Phone Number') }}</label>
                        <input type="text" name="phone" id="edit_phone" placeholder="+1 (555) 545-5421"
                            class="w-full h-[40px] mt-2.5 border border-neutral-100 rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <button type="submit"
                        class="rs-add-new-address-btn text-sm bg-linear-to-r from-cyan-500 to-blue-500 text-white h-[48px] text-center leading-[48px] w-[100%] rounded-xl">
                        {{ __('Update Address') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- manage addresses area -->
        <section class="rs-manage-addresses-section pt-[60px] pb-[80px] px-4 xl:px-0">
            <div class="rs-manage-addresses-area max-w-2lg mx-auto">
                @if (session('success'))
                    <div class="max-w-2lg mx-auto mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <button type="button" onclick="toggleModal('addAddressModal')"
                    class="rs-add-new-address-btn text-sm bg-linear-to-r from-cyan-500 to-blue-500 text-white mb-[30px] h-[48px] inline-flex items-center justify-center leading-[48px] w-[190px] sm:w-[290px] rounded-xl">
                    <i class="fa-solid fa-plus mr-[8px]"></i>
                    {{ __('Add New Address') }}
                </button>

                @forelse($addresses as $address)
                    <div
                        class="rs-manage-addresses-box bg-[white] rounded-xl p-[24px] mb-[24px] border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                        <div class="rs-manage-addresses-content flex gap-[20px] border-b-[1.5px] border-neutral-200 mb-[20px]">
                            <div
                                class="rs-manage-addresses-icon flex-none w-[48px] h-[48px] sm:w-[56px] sm:h-[56px] flex bg-mint-50 rounded-lg">
                                @if (isset($address->label) && strtolower($address->label) === 'home')
                                    <img src="{{ asset('website/assets/icons/home-icon.svg') }}" alt=""
                                        class="m-auto w-[20px] h-[20px] sm:w-[24px] sm:h-[24px]">
                                @elseif(isset($address->label) && strtolower($address->label) === 'office')
                                    <img src="{{ asset('website/assets/icons/city-icon.svg') }}" alt=""
                                        class="m-auto w-[20px] h-[20px] sm:w-[24px] sm:h-[24px]">
                                @else
                                    <img src="{{ asset('website/assets/icons/location-green.svg') }}" alt=""
                                        class="m-auto w-[20px] h-[20px] sm:w-[24px] sm:h-[24px]">
                                @endif
                            </div>
                            <div class="rs-manage-addresses-info">
                                <div class="rs-manage-addresses-info-top flex text-center gap-[12px] mb-[12px]">
                                    <span
                                        class="text-neutral-900 text-base leading-[140%] font-medium">{{ $address->address_name ?? __('Address') }}</span>
                                    @if ($address->is_default)
                                        <span
                                            class="text-mint-600 rounded-[25px] border-[1.5px] border-mint-200 flex gap-[4px] justify-center bg-mint-50 h-[24px] w-[78px] leading-[24px] text-xs font-normal">
                                            <img class="h-[10px] w-[10px] mt-auto mb-auto"
                                                src="{{ asset('website/assets/icons/star.svg') }}" alt="">
                                            {{ __('Default') }}
                                        </span>
                                    @endif
                                </div>
                                <div
                                    class="leading-[140%] mb-[6px] text-[12px] sm:text-sm font-normal flex gap-[6px] text-neutral-700">
                                    <img src="{{ asset('website/assets/icons/location.svg') }}" alt="">
                                    {{ implode(', ', array_filter([$address->house_no, $address->flat_no, $address->road_no, $address->block, $address->area, $address->address_line, $address->address_line2, $address->post_code])) }}
                                </div>
                                <a href="tel:{{ $address->phone ?? ($user->mobile ?? '') }}"
                                    class="leading-[140%] text-sm mb-[24px] font-normal flex gap-[6px] text-neutral-500">
                                    <img src="{{ asset('website/assets/icons/call.svg') }}" alt="" srcset="">
                                    {{ $address->phone ?? ($user->mobile ?? 'N/A') }}
                                </a>
                            </div>
                        </div>
                        <div class="rs-manage-addresses-box-bottom-area">
                            @if (!$address->is_default)
                                <div class="rs-manage-addresses-btn flex flex-wrap gap-[15px] md:flex-nowrap">
                                    <form action="{{ route('web.addresses.setDefault', $address->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        <button type="submit"
                                            class="leading-[40px] text-xs font-medium flex gap-[5px] text-neutral-500 rounded-[12px] border-[1.50px] border-neutral-50 w-[124px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                                            {{ __('Set as Default') }}
                                        </button>
                                    </form>
                                    <button type="button" data-id="{{ $address->id }}"
                                        data-name="{{ $address->address_name }}" data-house="{{ $address->house_no }}"
                                        data-flat="{{ $address->flat_no }}" data-road="{{ $address->road_no }}"
                                        data-block="{{ $address->block }}" data-area="{{ $address->area }}"
                                        data-address="{{ $address->address_line }}" data-post="{{ $address->post_code }}"
                                        data-phone="{{ $address->phone }}"
                                        class="edit-address-btn leading-[40px] text-xs font-medium flex gap-[5px] text-neutral-500 rounded-[12px] border-[1.50px] border-neutral-50 w-[88px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                                        <img class="h-[12px] w-[12px] mt-auto mb-auto"
                                            src="{{ asset('website/assets/icons/edit-btn.svg') }}" alt="">
                                        {{ __('Edit') }}
                                    </button>
                                    <form action="{{ route('web.addresses.destroy', $address->id) }}" method="POST"
                                        style="display: inline;">
                                        @csrf
                                        <button type="submit"
                                            class="leading-[40px] text-xs font-medium flex gap-[5px] text-danger-600 rounded-[12px] border-[1.50px] border-neutral-50 w-[88px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-danger-600 transition-all duration-300">
                                            <img class="h-[12px] w-[12px] mt-auto mb-auto"
                                                src="{{ asset('website/assets/icons/trash-red.svg') }}" alt="">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="rs-manage-addresses-edit-btn">
                                    <button type="button" data-id="{{ $address->id }}"
                                        data-name="{{ $address->address_name }}" data-house="{{ $address->house_no }}"
                                        data-flat="{{ $address->flat_no }}" data-road="{{ $address->road_no }}"
                                        data-block="{{ $address->block }}" data-area="{{ $address->area }}"
                                        data-address="{{ $address->address_line }}" data-post="{{ $address->post_code }}"
                                        data-phone="{{ $address->phone }}"
                                        class="edit-address-btn leading-[40px] text-xs font-medium flex gap-[5px] text-neutral-500 rounded-[12px] border-[1.50px] border-neutral-50 w-[88px] h-[40px] justify-center border border-transparent hover:border-[1.5px] hover:border-mint-600 transition-all duration-300">
                                        <img class="h-[12px] w-[12px] mt-auto mb-auto"
                                            src="{{ asset('website/assets/icons/edit-btn.svg') }}" alt="">
                                        {{ __('Edit') }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-8 rounded-3xl text-center">
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No') }} {{ __('Addresses') }} {{ __('Saved') }}</h3>
                        <p class="text-gray-500 mb-6">{{ __("You haven't added any delivery addresses yet.") }}</p>
                        <button type="button" onclick="toggleModal('addAddressModal')"
                            class="inline-block bg-mint-600 text-white px-6 py-3 rounded-lg hover:bg-mint-700 transition">
                            {{ __('Add New Address') }}
                        </button>
                    </div>
                @endforelse
            </div>
        </section>

        <script>
            function toggleModal(modalId) {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.toggle('hidden');
                }
            }

            // Handle edit button clicks
            document.querySelectorAll('.edit-address-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name || '';
                    const house = this.dataset.house || '';
                    const flat = this.dataset.flat || '';
                    const road = this.dataset.road || '';
                    const block = this.dataset.block || '';
                    const area = this.dataset.area || '';
                    const address = this.dataset.address || '';
                    const post = this.dataset.post || '';
                    const phone = this.dataset.phone || '';

                    document.getElementById('edit_address_name').value = name;
                    document.getElementById('edit_house_no').value = house;
                    document.getElementById('edit_flat_no').value = flat;
                    document.getElementById('edit_road_no').value = road;
                    document.getElementById('edit_block').value = block;
                    document.getElementById('edit_area').value = area;
                    document.getElementById('edit_address_line').value = address;
                    document.getElementById('edit_post_code').value = post;
                    document.getElementById('edit_phone').value = phone;
                    document.getElementById('editForm').action = `/addresses/${id}`;
                    toggleModal('editAddressModal');
                });
            });
        </script>
    @else
        <section class="py-20 text-center">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">{{ __('Please Login') }}</h2>
            <p class="text-gray-600 mb-6">{{ __('You need to login to view your addresses.') }}</p>
            <a href="{{ route('login') }}" class="bg-mint-600 text-white px-6 py-3 rounded-lg hover:bg-mint-700 transition">
                {{ __('Login') }}
            </a>
        </section>
    @endauth
@endsection
