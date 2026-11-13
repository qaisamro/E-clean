<?php $__env->startSection('content'); ?>
<div class="row px-5">
    <div class="col-6 col-md-4 ms-auto d-flex align-items-center me-5" style="height: 100vh;" id="mainCard">
        <div class="card w-100 pt-3" style="overflow: hidden;border-bottom: none;">
            <div class="loader" id="loader" style="display: none">
                <img src="https://i.ibb.co/9mWzCWCt/cog-loader.gif" alt="cog" border="0">
            </div>
            <h2 class="text-center mt-3">Ready For Installation</h2>
            <p class="fs-7 text-center">
                <strong class="text-danger">You will need to fill up the following mandatory items before proceeding.</strong>
            </p>

            <div class="w-80 m-auto">
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $environmentFields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $types): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <form method="post" id="form_<?php echo e(($key + 1)); ?>" style="display: <?php echo e($key > 0 ? 'none':null); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $fields): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($fields['option'])): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fields['type'] == 'select'): ?>
                                <div class="mb-3">
                                    <label for=""><?php echo e($fields['label']); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(substr($fields['rule'], 0, 8) === 'required'): ?><strong class="text-danger">*</strong><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                    <select name="<?php echo e($name); ?>" class="form-control">
                                        <option value=""><?php echo e($fields['placeholder']); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fields['option']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option <?php echo e(isset($fields['value']) &&  $fields['value'] == $option ? 'selected':''); ?> value="<?php echo e($option); ?>"><?php echo e($option); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </select>
                                </div>
                            <?php else: ?>
                            <div class="mb-3">
                                <label for=""><?php echo e($fields['label']); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(substr($fields['rule'], 0, 8) === 'required'): ?><strong class="text-danger">*</strong><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label> <br>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fields['option']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label for="radio_<?php echo e($option); ?>" class="<?php echo e($k > 0 ? 'ms-4':''); ?>">
                                    <?php echo e($option ? 'True':'False'); ?> <input type="radio" id="radio_<?php echo e($option); ?>" <?php echo e(!$option ? 'checked':''); ?> name="<?php echo e($name); ?>" value="<?php echo e($option ? 'true':'false'); ?>" class="form-control-radio">
                                </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <div class="mb-3">
                                <label for=""><?php echo e($fields['label']); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(substr($fields['rule'], 0, 8) === 'required'): ?><strong class="text-danger">*</strong><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                <input type="<?php echo e($fields['type']); ?>" name="<?php echo e($name); ?>" value="<?php echo e(isset($fields['value']) ? $fields['value'] : ''); ?>" placeholder="<?php echo e($fields['placeholder']); ?>" class="form-control">
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="d-flex mt-3">
                        <div class="me-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 16 16">
                                <g id="Group_22706" data-name="Group 22706"
                                    transform="translate(-704 -571)">
                                    <g id="Rectangle_19036" data-name="Rectangle 19036"
                                        transform="translate(704 571)" fill="#fff" stroke="#ea4335"
                                        stroke-width="1">
                                        <rect width="16" height="16" rx="8" stroke="none">
                                        </rect>
                                        <rect x="0.5" y="0.5" width="15" height="15" rx="7.5"
                                            fill="none"></rect>
                                    </g>
                                    <g id="Group_22693" data-name="Group 22693"
                                        transform="translate(0 -12)">
                                        <g id="Group_22698" data-name="Group 22698">
                                            <rect id="Rectangle_19044" data-name="Rectangle 19044"
                                                width="1.5" height="5" rx="0.75"
                                                transform="translate(715.475 589.939) rotate(45)"
                                                fill="#ea4335"></rect>
                                            <rect id="Rectangle_19111" data-name="Rectangle 19111"
                                                width="1.5" height="5" rx="0.75"
                                                transform="translate(716.536 591) rotate(135)"
                                                fill="#ea4335"></rect>
                                            <rect id="Rectangle_19051" data-name="Rectangle 19051"
                                                width="8" height="1.5" rx="0.75"
                                                transform="translate(708 590.25)" fill="#ea4335"></rect>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </div>
                        <p class="ml-2 mb-0 fs-7 fw-500" style="color: #666; line-height: 18px;">
                            Ensure that the page remains unrefreshed and is not skipped, and confirm that your internet connection remains active.
                        </p>
                    </div>

                    <div class="my-4 py-4 absolute-bottom-left right-0 d-flex justify-content-center">
                        <button onclick="submitData('form_<?php echo e(($key + 1)); ?>', 'form_<?php echo e(($key + 2)); ?>', '<?php echo e(route('installer.app-configure.store', $key)); ?>')" type="button" class="btn btn-install text-uppercase">Next</button>
                    </div>
                </form>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            

             
             <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(config('installer.verify_purchase')): ?>
                <form method="post" id="form_<?php echo e(($finalForm - 1)); ?>" style="display:none">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $verifyRules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $name => $verifyRule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="mb-3">
                        <label for=""><?php echo e($verifyRule['label']); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(substr($fields['rule'], 0, 8) === 'required'): ?><strong class="text-danger">*</strong><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                        <input type="<?php echo e($verifyRule['type']); ?>" name="<?php echo e($name); ?>" placeholder="<?php echo e($verifyRule['placeholder']); ?>" class="form-control">
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="my-4 py-4 absolute-bottom-left right-0 d-flex justify-content-center">
                        <button onclick='verifyPurchase(`<?php echo e(route("installer.verify-perchase")); ?>`, "form_<?php echo e(($finalForm - 1)); ?>", "form_<?php echo e($finalForm); ?>")' type="button" class="btn btn-install text-uppercase">Verify Purchase</button>
                    </div>
                </form>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            

            
            <span id="form_<?php echo e($finalForm); ?>" style="display: none">
                <div class="d-flex mt-3">
                    <div class="me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 16 16">
                            <g id="Group_22706" data-name="Group 22706"
                                transform="translate(-704 -571)">
                                <g id="Rectangle_19036" data-name="Rectangle 19036"
                                    transform="translate(704 571)" fill="#fff" stroke="#ea4335"
                                    stroke-width="1">
                                    <rect width="16" height="16" rx="8" stroke="none">
                                    </rect>
                                    <rect x="0.5" y="0.5" width="15" height="15" rx="7.5"
                                        fill="none"></rect>
                                </g>
                                <g id="Group_22693" data-name="Group 22693"
                                    transform="translate(0 -12)">
                                    <g id="Group_22698" data-name="Group 22698">
                                        <rect id="Rectangle_19044" data-name="Rectangle 19044"
                                            width="1.5" height="5" rx="0.75"
                                            transform="translate(715.475 589.939) rotate(45)"
                                            fill="#ea4335"></rect>
                                        <rect id="Rectangle_19111" data-name="Rectangle 19111"
                                            width="1.5" height="5" rx="0.75"
                                            transform="translate(716.536 591) rotate(135)"
                                            fill="#ea4335"></rect>
                                        <rect id="Rectangle_19051" data-name="Rectangle 19051"
                                            width="8" height="1.5" rx="0.75"
                                            transform="translate(708 590.25)" fill="#ea4335"></rect>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <p class="ml-2 mb-0 fs-7 fw-500" style="color: #666; line-height: 18px;">
                        Congratulations, you have successfully stored all the necessary information in the .env file. This step is crucial for ensuring the proper functionality of the application, and you've completed it accurately.
                    </p>
                </div>

                <div class="d-flex mt-3">
                    <div class="me-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 16 16">
                            <g id="Group_22706" data-name="Group 22706"
                                transform="translate(-704 -571)">
                                <g id="Rectangle_19036" data-name="Rectangle 19036"
                                    transform="translate(704 571)" fill="#fff" stroke="#e6e6e6"
                                    stroke-width="1">
                                    <rect width="16" height="16" rx="8"
                                        stroke="none"></rect>
                                    <rect x="0.5" y="0.5" width="15" height="15"
                                        rx="7.5" fill="none"></rect>
                                </g>
                                <g id="Group_22693" data-name="Group 22693"
                                    transform="translate(0 -12)">
                                    <g id="Group_22698" data-name="Group 22698">
                                        <rect id="Rectangle_19044" data-name="Rectangle 19044"
                                            width="1.5" height="5" rx="0.75"
                                            transform="translate(715.475 589.939) rotate(45)"
                                            fill="#007cff"></rect>
                                        <rect id="Rectangle_19111" data-name="Rectangle 19111"
                                            width="1.5" height="5" rx="0.75"
                                            transform="translate(716.536 591) rotate(135)"
                                            fill="#007cff"></rect>
                                        <rect id="Rectangle_19051" data-name="Rectangle 19051"
                                            width="8" height="1.5" rx="0.75"
                                            transform="translate(708 590.25)" fill="#007cff"></rect>
                                    </g>
                                </g>
                            </g>
                        </svg>
                    </div>
                    <p class="ml-2 mb-0 fs-7 fw-500" style="color: #666; line-height: 18px;">
                        You've reached the final step. Once you click on the final submission, your application will be activated, provided all the information is accurate.
                    </p>
                </div>

                <div class="my-4 py-4 absolute-bottom-left right-0 d-flex justify-content-center">
                    <button type="button" onclick="finalSubmit()" class="btn btn-install text-uppercase">Final Submission</button>
                </div>
            </span>
            

            </div>

            <div class="row">
                <div class="col" style="min-height: 3px; background: #006a4e"></div>
                <div class="col" style="min-height: 3px;  background: #f42a41"></div>
                <div class="col" style="min-height: 3px;  background: #006a4e"></div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" value="<?php echo e(csrf_token()); ?>" id="csrf_tokon">
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        const finalUrl = "/install/app-final-installation"
        const csrfUrl = "/install/refresh-csrf-token"
        const purchaseEnable = "<?php echo e(config('installer.verify_purchase')); ?>"
        const lastForm = "<?php echo e(count($environmentFields)); ?>"

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        function submitData(formId, nextId, url){
            let formData = $('#' + formId).serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});

            let currentForm = document.getElementById(formId)
            let nextForm = document.getElementById(nextId)
            let loader = document.getElementById('loader');
            let csrfToken = $('#csrf_tokon').val()

            loader.style.display = 'flex'
            $.ajax({
                url : url,
                type : "POST",
                dataType : "json",
                data: formData,
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                success:function(data)
                {
                    loader.style.display = 'none'
                    if(data.status == 400){
                        Swal.fire({
                            title: 'Wrong!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Try Again'
                        })
                    }

                    if(data.status == 200){
                        setNewToken()
                        Toast.fire({
                            icon: "success",
                            title: "The environment setup is successful."
                        });
                        currentForm.style.display = 'none'
                        nextForm.style.display = 'block'
                    }
                },
                error:function(jqXHR, textStatus, errorThrown) {
                    // Check if the server returned a JSON response
                    if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                        var errors = jqXHR.responseJSON.errors;
                        // Loop through the errors and display them in your fields
                        for (var field in errors) {
                            if (errors.hasOwnProperty(field)) {
                                $(`input[name="${field}"]`).addClass("is-invalid")
                                $(`select[name="${field}"]`).addClass("is-invalid")

                                var errorMessage = errors[field];
                            }

                        }
                    }
                    loader.style.display = 'none'
                }
            });
        }

        function verifyPurchase(url, formId, nextId){
            let formData = $('#' + formId).serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});

            let currentForm = document.getElementById(formId)
            let nextForm = document.getElementById(nextId)

            console.log(formData);
            let csrfToken = $('#csrf_tokon').val()
            loader.style.display = 'flex'
            $.ajax({
                url : url,
                type : "POST",
                dataType : "json",
                data: formData,
                headers: {
                    'X-CSRF-Token': csrfToken
                },
                success:function(data)
                {
                    loader.style.display = 'none'
                    if(data.status === 422){
                        Swal.fire({
                            title: 'Wrong!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonText: 'Try Again'
                        })
                    }
                    if(data.status === 200){
                        Toast.fire({
                            icon: "success",
                            title: data.message
                        });
                        currentForm.style.display = 'none'
                        nextForm.style.display = 'block'
                    }
                },
                error:function(jqXHR, textStatus, errorThrown) {
                    if (jqXHR.responseJSON && jqXHR.responseJSON.errors) {
                        var errors = jqXHR.responseJSON.errors;
                        // Loop through the errors and display them in your fields
                        for (var field in errors) {
                            if (errors.hasOwnProperty(field)) {
                                $(`input[name="${field}"]`).addClass("is-invalid")
                                $(`select[name="${field}"]`).addClass("is-invalid")

                                var errorMessage = errors[field];
                            }

                        }
                    }
                    loader.style.display = 'none'
                }
            })
        }

        function finalSubmit(){
            loader.style.display = 'flex'
            $.ajax({
                url : finalUrl,
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    document.getElementById('mainCard').remove()
                    Swal.fire({
                        title: 'Congratulation',
                        text: 'Our Installation system is perfectly done please wait. You will be redirected within 10 seconds.',
                        icon: 'success',
                        timer: 5000,
                        showConfirmButton: true,
                        confirmButtonText: 'Click To Redirect',
                        willClose: () => {
                            window.location.href = '/';
                        }
                    });
                },
                error:function(jqXHR, textStatus, errorThrown) {
                    let error = JSON.parse(jqXHR.responseText)
                    console.log(error);
                    Swal.fire({
                        title: 'Something went wrong!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonText: 'Try Again'
                    })
                    loader.style.display = 'none'
                }
            })
        }

        function setNewToken(){
            $.ajax({
                url : csrfUrl,
                type : "GET",
                dataType : "json",
                success:function(data)
                {
                    let tokenInput = document.getElementById('csrf_tokon');
                    tokenInput.value = data.token
                }
            })
        }

    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('joynala.web-installer::app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Lenovo\OneDrive\Desktop\E_Clean\Web_Admin\vendor\joynala\web-installer\resources\views\install.blade.php ENDPATH**/ ?>