  <div class="sidebar site-drawer fixed top-0 left-0 w-full h-full text-white z-50"
        id="sidebar">
        <div class="site-drawer-backdrop h-full w-full flex justify-center items-center flex-col relative" onclick="toggleSidebar()">
            <div class="site-drawer-sheet" onclick="event.stopPropagation()">
            <div class="site-drawer-head">
                <span class="site-drawer-title"><?php echo e(__('Menu')); ?></span>
            <button
                class="site-drawer-close absolute top-6 right-6 border h-10 w-10 rounded-lg flex justify-center items-center"
                onclick="toggleSidebar()" aria-label="Close menu">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            </div>

            <ul class="site-drawer-links space-y-2 flex flex-col items-stretch">
                <li onclick="toggleSidebar()">
                    <a href="#" class="site-drawer-link text-lg cursor-pointer"><?php echo e(__('Home')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#pricing" class="site-drawer-link text-lg cursor-pointer"><?php echo e(__('Services')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#features" class="site-drawer-link text-lg cursor-pointer"><?php echo e(__('Nearest Store')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#services" class="site-drawer-link text-lg cursor-pointer"><?php echo e(__('FAQ')); ?></a>
                </li>
                <li onclick="toggleSidebar()">
                    <a href="#support" class="site-drawer-link text-lg cursor-pointer"><?php echo e(__('Contact')); ?></a>
                </li>
            </ul>

            </div>
        </div>
    </div>
<?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\resources\views\website\layouts\includes\sidebar.blade.php ENDPATH**/ ?>