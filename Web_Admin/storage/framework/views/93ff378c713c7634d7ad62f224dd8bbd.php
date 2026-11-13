<?php $__env->startSection('content'); ?>
    <!-- manage-address-modal -->
    <section id="role" class="modal_container">

        <!-- backdrop -->
        <div onclick="toggleModal('role')" class="modal_backdrop"></div>

        <!-- modal content -->
        <div class="modal_content">
            <form action="#" class="rs-manage-addresses-form">
                <div class="flex justify-between items-center mb-[30px]">
                    <h3 class="text-neutral-700 text-lg font-semibold"><?php echo e(__('Add New Address')); ?></h3>
                    <button type="button" onclick="toggleModal('role')"
                        class="transition-transform duration-300 hover:rotate-90">
                        <img src="<?php echo e(asset('website/assets/icons/close.svg')); ?>" alt="">
                    </button>
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Label')); ?></label>
                    <input type="text" placeholder="<?php echo e(__('Example')); ?> : <?php echo e(__('Home')); ?>, <?php echo e(__('Office')); ?> ..."
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Street Address')); ?></label>
                    <input type="text" placeholder="123 Lovely Road, Apt 6B"
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="flex gap-3 mb-4">
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('City')); ?></label>
                        <input type="text" placeholder="New York"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                    <div class="w-[50%]">
                        <label class="text-neutral-700 text-base font-medium"><?php echo e(__('State')); ?></label>
                        <input type="text" placeholder="NY"
                            class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                    </div>
                </div>
                <div class="mb-4">
                    <label class="text-neutral-700 text-base font-medium"><?php echo e(__('ZIP Code')); ?></label>
                    <input type="text" placeholder="10003"
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <div class="mb-[30px]">
                    <label class="text-neutral-700 text-base font-medium"><?php echo e(__('Phone Number')); ?></label>
                    <input type="text" placeholder="+1 (555) 545-5421"
                        class="w-full h-[40px] mt-2.5 border border-[#E2E8F0] rounded-[12px] px-[16px] py-[10px]" />
                </div>
                <button
                    class="rs-add-new-address-btn text-sm bg-[linear-gradient(135deg,#028090,#00A896)] text-white h-[48px] text-center leading-[48px] w-[100%] rounded-[14px]">
                    <?php echo e(__('Save Address')); ?>

                </button>
            </form>
        </div>
    </section>

    <!-- navbar -->

    <!-- Sidebar -->
    <div class="sidebar fixed top-0 left-0 w-full h-full bg-gradient-to-tl from-[#083744] via-[#156172] to-[#028090] text-white  z-50"
        id="sidebar">
        <div class="h-full w-full flex justify-center items-center flex-col relative">

            <button class="absolute top-6 right-6 border h-10 w-10 border-white rounded-[12px] flex justify-center items-center"
                onclick="toggleSidebar()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <ul class="space-y-4 flex flex-col items-center">
                <li onclick="toggleSidebar()">
                    <a href="#" class="text-lg hover:text-blue-400 cursor-pointer"><?php echo e(__('Home')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#pricing" class="text-lg hover:text-blue-400 cursor-pointer"><?php echo e(__('Services')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#features" class="text-lg hover:text-blue-400 cursor-pointer"><?php echo e(__('Nearest Store')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#services" class="text-lg hover:text-blue-400 cursor-pointer"><?php echo e(__('FAQ')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#support" class="text-lg hover:text-blue-400 cursor-pointer"><?php echo e(__('Contact')); ?></a>
                </li>
            </ul>
        </div>
    </div>


    <main style="background-color:#FAF7F2;" class="space-y-[60px]">
        <!-- breadcrumb -->
        <section
            style="background:linear-gradient(135deg,#083744,#156172),url('<?php echo e(asset('website/assets/images/header/breadcrumb.png')); ?>');background-size:cover;background-position:center;" class="rs-breadcrumb-area bg-[linear-gradient(135deg,#083744,#156172)] h-[260px] w-full bg-[url('<?php echo e(asset('website/assets/images/header/breadcrumb.png')); ?>')] bg-cover bg-center flex flex-col items-center justify-center text-center">
            <div class="rs-breadcrumb-content">
                <h1
                    class="rs-breadcrumb-title mb-[5px] sm:mb-[10px] text-[26px] md:text-[30px]  md:text-4xl text-white font-semibold leading-[140%]">
                    <?php echo e(__('Frequently Asked Questions')); ?>

                </h1>
                <div class="rs-breadcrumb-top-content">
                    <a href="<?php echo e(route('web.home')); ?>"
                        class="text-base md:text-lg text-white font-normal leading-[100%]"><?php echo e(__('Home / ')); ?></a>
                    <a href="<?php echo e(route('web.faq')); ?>"
                        class="text-base md:text-lg text-white font-normal leading-[100%]"><?php echo e(__('FAQ')); ?></a>
                </div>
            </div>
        </section>

        <!-- tabs - dynamic categories -->
        <section class="max-w-2lg mx-auto px-4 xl-1:px-0 flex justify-center items-center flex-wrap  gap-3">
            <button onclick="filterFaqs('all')"
                class="transition-all duration-150 h-10 w-fit px-3 md:px-5 rounded-[14px] group hover:bg-gradient-to-bl hover:from-[#028090] hover:via-[#00A896] hover:to-[#00A896]  border-[1.5px] border-[#E2E8F0] hover:border-transparent category-btn active"
                data-category="all">
                <p
                    class="text-[10px] md:text-xs font-medium text-center transition-all duration-150 text-gray-500 group-hover:text-white category-name">
                    <?php echo e(__('All Questions')); ?></p>
            </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button onclick="filterFaqs('<?php echo e($category->id); ?>')"
                    class="transition-all duration-150 h-10 w-fit px-3 md:px-5 rounded-[14px] group hover:bg-gradient-to-bl hover:from-[#028090] hover:via-[#00A896] hover:to-[#00A896]  border-[1.5px] border-[#E2E8F0] hover:border-transparent category-btn"
                    data-category="<?php echo e($category->id); ?>">
                    <p
                        class="text-[10px] md:text-xs font-medium text-center transition-all duration-150 text-gray-500 group-hover:text-white category-name">
                        <?php echo e($category->name); ?></p>
                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </section>


        
        <section class="max-w-2lg mx-auto px-4 xl-1:px-0">
            <div class="w-full">

                
                <div class="category-section mb-8" data-category-id="all">
                    <?php
                        $allFaqs = $faqs->where('status', 'active');
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($allFaqs->count() > 0): ?>
                        <div id="accordion-all" data-accordion="single" class="space-y-4">
                            <?php $counter = 0; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $allFaqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $items = json_decode($faq->content, true);
                                ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($items) && count($items) > 0): ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($q['ques'], $q['answer'])): ?>
                                            <section
                                                class="shadow-lg shadow-[#E6F4F1] p-4 flex justify-start items-start gap-4 bg-white rounded-[20px]">

                                                <div
                                                    class="bg-[#E6F4F1] rounded-full h-10 w-10 min-h-10 min-w-10 flex justify-center items-center">
                                                    <img src="<?php echo e(asset('website/assets/icons/question.svg')); ?>"
                                                        class="h-5 w-5" alt="">
                                                </div>

                                                <div class="w-full">
                                                    <button type="button"
                                                        class="w-full flex items-center justify-between gap-3 text-left"
                                                        aria-expanded="false"
                                                        aria-controls="acc-panel-all-<?php echo e($counter); ?>"
                                                        id="acc-trigger-all-<?php echo e($counter); ?>">

                                                        <div class="flex-1 flex justify-between items-center mt-2">
                                                            <p class="text-sm md:text-base font-semibold text-gray-700">
                                                                <?php echo e($q['ques']); ?>

                                                            </p>

                                                            <img src="<?php echo e(asset('website/assets/icons/chevron-down.svg')); ?>"
                                                                class="transition-all duration-300 rotate-180"
                                                                alt="">
                                                        </div>
                                                    </button>

                                                    <div id="acc-panel-all-<?php echo e($counter); ?>" class="hidden mt-3">
                                                        <div class="overflow-hidden">
                                                            <p class="pt-4 text-slate-700 text-sm sm:text-base">
                                                                <?php echo nl2br(e($q['answer'])); ?>

                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>

                                            <?php $counter++; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categories->count() > 0): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $categoryFaqs = $faqs->where('faq_category_id', $category->id)->where('status', 'active');
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($categoryFaqs->count() > 0): ?>
                            <div class="category-section mb-8 hidden" data-category-id="<?php echo e($category->id); ?>">
                                <div id="accordion-<?php echo e($category->id); ?>" data-accordion="single" class="space-y-4">
                                    <?php $counter = 0; ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $categoryFaqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $items = json_decode($faq->content, true);
                                        ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_array($items) && count($items) > 0): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($q['ques'], $q['answer'])): ?>
                                                    <section
                                                        class="shadow-lg shadow-[#E6F4F1] p-4 flex justify-start items-start gap-4 bg-white rounded-[20px]">

                                                        <div
                                                            class="bg-[#E6F4F1] rounded-full h-10 w-10 min-h-10 min-w-10 flex justify-center items-center">
                                                            <img src="<?php echo e(asset('website/assets/icons/question.svg')); ?>"
                                                                class="h-5 w-5" alt="">
                                                        </div>

                                                        <div class="w-full">
                                                            <button type="button"
                                                                class="w-full flex items-center justify-between gap-3 text-left"
                                                                aria-expanded="false"
                                                                aria-controls="acc-panel-<?php echo e($category->id); ?>-<?php echo e($counter); ?>"
                                                                id="acc-trigger-<?php echo e($category->id); ?>-<?php echo e($counter); ?>">

                                                                <div class="flex-1 flex justify-between items-center mt-2">
                                                                    <p
                                                                        class="text-sm md:text-base font-semibold text-gray-700">
                                                                        <?php echo e($q['ques']); ?>

                                                                    </p>

                                                                    <img src="<?php echo e(asset('website/assets/icons/chevron-down.svg')); ?>"
                                                                        class="transition-all duration-300 rotate-180"
                                                                        alt="">
                                                                </div>
                                                            </button>

                                                            <div id="acc-panel-<?php echo e($category->id); ?>-<?php echo e($counter); ?>"
                                                                class="hidden mt-3">
                                                                <div class="overflow-hidden">
                                                                    <p class="pt-4 text-slate-700 text-sm sm:text-base">
                                                                        <?php echo nl2br(e($q['answer'])); ?>

                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </section>

                                                    <?php $counter++; ?>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            </div>
        </section>

        <!-- still have questions -->
        <section class="max-w-2lg mx-auto px-4 xl-1:px-0 py-[30px] md:py-[60px] relative bg-cover bg-no-repeat min-h-52"
            style="background-image: url('../assets/images/stores-network/bg.png');">
            <div class="absolute top-0 right-0 w-full h-full z-0 bg-[#028090]/5"></div>
            <div class="relative z-10 p-4 flex justify-center items-center flex-col gap-[30px]">
                <div class="space-y-[10px]">
                    <p class="text-2xl md:text-[32px] font-semibold text-center text-gray-900">
                        <?php echo e(__('Still have questions?')); ?>

                    </p>

                    <p class="text-sm md:text-lg text-center text-gray-700">
                        <?php echo e(__("Can't find the answer you're looking for? Our customer support team is here to help.")); ?>

                    </p>
                </div>
                <div class="flex flex-col sm:flex-row gap-[15px]">
                    <button
                        class="flex justify-center items-center w-full sm:w-fit h-14 overflow-hidden px-4 md:px-6 py-2 md:py-4 rounded-[14px] bg-[linear-gradient(135deg,#028090,#00A896)]">
                        <a href="<?php echo e(route('web.contact')); ?>">
                            <p class="text-sm md:text-base font-bold text-center text-white">
                                <?php echo e(__('Contact Support')); ?>

                            </p>
                        </a>
                    </button>
                    <button
                        class="flex justify-center items-center w-full sm:w-fit h-14 overflow-hidden px-4 md:px-6 py-2 md:py-4 rounded-[14px] border-[1.5px] border-[#028090] bg-white">
                        <a href="<?php echo e(route('web.home')); ?>">
                            <p class="text-sm md:text-base font-bold text-center text-[#028090]">
                                <?php echo e(__('Back To Home')); ?>

                            </p>
                        </a>
                    </button>
                </div>
            </div>
        </section>


        <!-- get started -->
        <section style="background:linear-gradient(135deg,#083744,#156172);" class="bg-[linear-gradient(135deg,#083744,#156172)] ">
            <div
                class="max-w-2lg mx-auto py-[60px] px-4 xl-1:px-0 flex flex-col lg:flex-row justify-center lg:justify-between items-center gap-6 lg:gap-0">
                <div class="flex-1">
                    <p class="text-[32px] font-semibold text-center lg:text-left text-white ">
                        <?php echo e(__('Ready to Get Started?')); ?>

                    </p>
                    <p class="text-lg text-center lg:text-left text-neutral-50">
                        <?php echo e(__('Book your first order and experience hassle-free dry cleaning service')); ?>

                    </p>
                </div>
                <div class="flex flex-col md:flex-row justify-center items-center gap-4">
                    <button class="btn_solid_white_lg">
                        <a href="<?php echo e(route('web.service')); ?>">
                            <p><?php echo e(__('Book Now')); ?></p>
                        </a>
                        <img src="<?php echo e(asset('website/assets/icons/arrow-left-green.svg')); ?>" alt="">
                    </button>



                    <button class="btn_outline_white_lg">
                        <p><?php echo e(__('Become a Partner')); ?></p>
                    </button>
                </div>
            </div>
        </section>
    </main>

    <script>
        function filterFaqs(categoryId) {
            // Update button states
            document.querySelectorAll('.category-btn').forEach(btn => {
                const nameElement = btn.querySelector('.category-name');
                if (btn.dataset.category === categoryId) {
                    btn.classList.add('active');
                    btn.classList.add('bg-gradient-to-bl', 'from-[#028090]', 'via-[#00A896]', 'to-[#00A896]');
                    btn.classList.remove('border-[#E2E8F0]');
                    btn.classList.add('border-transparent');
                    nameElement.classList.remove('text-gray-500');
                    nameElement.classList.add('text-white');
                } else {
                    btn.classList.remove('active');
                    btn.classList.remove('bg-gradient-to-bl', 'from-[#028090]', 'via-[#00A896]', 'to-[#00A896]');
                    btn.classList.remove('border-transparent');
                    btn.classList.add('border-[#E2E8F0]');
                    nameElement.classList.add('text-gray-500');
                    nameElement.classList.remove('text-white');
                }
            });

            // Show/hide sections
            document.querySelectorAll('.category-section').forEach(section => {
                if (categoryId === 'all' || section.dataset.categoryId === categoryId) {
                    section.classList.remove('hidden');
                } else {
                    section.classList.add('hidden');
                }
            });
        }

        document.addEventListener("DOMContentLoaded", function() {
            // Set "<?php echo e(__('All Questions')); ?>" as selected by default
            filterFaqs('all');

            document.querySelectorAll('[id^="acc-trigger-"]').forEach(button => {

                button.addEventListener('click', function() {

                    const panelId = this.getAttribute('aria-controls');
                    const panel = document.getElementById(panelId);

                    if (!panel) return;

                    panel.classList.toggle('hidden');

                    // rotate arrow icon
                    const icon = this.querySelector('img');
                    if (icon) {
                        icon.classList.toggle('rotate-180');
                    }
                });

            });

        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('website.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\pages\faq.blade.php ENDPATH**/ ?>