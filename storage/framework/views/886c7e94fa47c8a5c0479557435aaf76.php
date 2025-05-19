<!-- resources/views/auth/login.blade.php -->
<?php if (isset($component)) { $__componentOriginal69dc84650370d1d4dc1b42d016d7226b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b = $attributes; } ?>
<?php $component = App\View\Components\GuestLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\GuestLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div style="min-height: 100vh; background-color: #1a1a1a; display: flex; align-items: center; justify-content: center;">
        <div style="background-color: #2d2d2d; padding: 32px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px;">
            <div style="margin-bottom: 24px; text-align: center; color: #e0e0e0;">
                <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 8px;"><?php echo e(tenant('id')); ?></h2>
                <p style="font-size: 14px; opacity: 0.75;"><?php echo e(request()->getHost()); ?></p>
            </div>

            <?php if(session('status')): ?>
                <div style="margin-bottom: 16px; font-size: 14px; color: #e0e0e0;">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('login')); ?>" style="width: 100%;">
                <?php echo csrf_field(); ?>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <input 
                            type="email" 
                            name="email" 
                            value="<?php echo e(old('email')); ?>" 
                            required 
                            placeholder="Email"
                            style="width: 100%; padding: 8px 16px; background-color: #404040; border: 1px solid #606060; border-radius: 6px; color: #e0e0e0; outline: none;"
                        />
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p style="margin-top: 4px; font-size: 14px; color: #f87171;"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            placeholder="Password"
                            style="width: 100%; padding: 8px 16px; background-color: #404040; border: 1px solid #606060; border-radius: 6px; color: #e0e0e0; outline: none;"
                        />
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p style="margin-top: 4px; font-size: 14px; color: #f87171;"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div style="display: flex; align-items: center;">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            id="remember" 
                            style="margin-right: 8px; background-color: #404040; border: 1px solid #606060;"
                        />
                        <label for="remember" style="font-size: 14px; color: #e0e0e0;">Beni Hatırla</label>
                    </div>

                    <button 
                        type="submit"
                        style="width: 100%; padding: 8px 16px; background-color: #2563eb; border: none; border-radius: 6px; color: white; font-weight: 600; cursor: pointer; transition: background-color 0.2s;"
                        onmouseover="this.style.backgroundColor='#1d4ed8'"
                        onmouseout="this.style.backgroundColor='#2563eb'"
                    >
                        Giriş Yap
                    </button>
                </div>
            </form>

            <div style="margin-top: 20px; border-top: 1px solid #4a4a4a; padding-top: 16px;">
                <h3 style="font-size: 16px; color: #e0e0e0; margin-bottom: 12px; text-align: center;">Hazır Kullanıcılar</h3>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <?php
                        $host = request()->getHost();
                    ?>

                    <?php if($host === 'laravel.test'): ?>
                    <button 
                        type="button"
                        onclick="fillDemoUser('laravel')"
                        style="width: 100%; padding: 8px 16px; background-color: #4b5563; border: none; border-radius: 6px; color: white; font-weight: 500; cursor: pointer; transition: background-color 0.2s; display: flex; justify-content: space-between; align-items: center;"
                        onmouseover="this.style.backgroundColor='#374151'"
                        onmouseout="this.style.backgroundColor='#4b5563'"
                    >
                        <span>Central: laravel@test</span>
                        <span style="background-color: #1a1a1a; padding: 2px 6px; border-radius: 4px; font-size: 12px;">Admin</span>
                    </button>
                    <?php endif; ?>
                    
                    <!-- Root kullanıcıları her sayfada göster -->
                    <button 
                        type="button"
                        onclick="fillDemoUser('nurullah')"
                        style="width: 100%; padding: 8px 16px; background-color: #4b5563; border: none; border-radius: 6px; color: white; font-weight: 500; cursor: pointer; transition: background-color 0.2s; display: flex; justify-content: space-between; align-items: center;"
                        onmouseover="this.style.backgroundColor='#374151'"
                        onmouseout="this.style.backgroundColor='#4b5563'"
                    >
                        <span>Root: nurullah@nurullah.net</span>
                        <span style="background-color: #1a1a1a; padding: 2px 6px; border-radius: 4px; font-size: 12px;">Root</span>
                    </button>
                    
                    <button 
                        type="button"
                        onclick="fillDemoUser('turkbilisim')"
                        style="width: 100%; padding: 8px 16px; background-color: #4b5563; border: none; border-radius: 6px; color: white; font-weight: 500; cursor: pointer; transition: background-color 0.2s; display: flex; justify-content: space-between; align-items: center;"
                        onmouseover="this.style.backgroundColor='#374151'"
                        onmouseout="this.style.backgroundColor='#4b5563'"
                    >
                        <span>Root: info@turkbilisim.com.tr</span>
                        <span style="background-color: #1a1a1a; padding: 2px 6px; border-radius: 4px; font-size: 12px;">Root</span>
                    </button>
                    
                    <?php if($host === 'a.test'): ?>
                    <button 
                        type="button"
                        onclick="fillDemoUser('a')"
                        style="width: 100%; padding: 8px 16px; background-color: #4b5563; border: none; border-radius: 6px; color: white; font-weight: 500; cursor: pointer; transition: background-color 0.2s; display: flex; justify-content: space-between; align-items: center;"
                        onmouseover="this.style.backgroundColor='#374151'"
                        onmouseout="this.style.backgroundColor='#4b5563'"
                    >
                        <span>Tenant A: a@test</span>
                        <span style="background-color: #1a1a1a; padding: 2px 6px; border-radius: 4px; font-size: 12px;">Admin</span>
                    </button>
                    <?php endif; ?>
                    
                    <?php if($host === 'b.test'): ?>
                    <button 
                        type="button"
                        onclick="fillDemoUser('b')"
                        style="width: 100%; padding: 8px 16px; background-color: #4b5563; border: none; border-radius: 6px; color: white; font-weight: 500; cursor: pointer; transition: background-color 0.2s; display: flex; justify-content: space-between; align-items: center;"
                        onmouseover="this.style.backgroundColor='#374151'"
                        onmouseout="this.style.backgroundColor='#4b5563'"
                    >
                        <span>Tenant B: b@test</span>
                        <span style="background-color: #1a1a1a; padding: 2px 6px; border-radius: 4px; font-size: 12px;">Admin</span>
                    </button>
                    <?php endif; ?>
                    
                    <?php if($host === 'c.test'): ?>
                    <button 
                        type="button"
                        onclick="fillDemoUser('c')"
                        style="width: 100%; padding: 8px 16px; background-color: #4b5563; border: none; border-radius: 6px; color: white; font-weight: 500; cursor: pointer; transition: background-color 0.2s; display: flex; justify-content: space-between; align-items: center;"
                        onmouseover="this.style.backgroundColor='#374151'"
                        onmouseout="this.style.backgroundColor='#4b5563'"
                    >
                        <span>Tenant C: c@test</span>
                        <span style="background-color: #1a1a1a; padding: 2px 6px; border-radius: 4px; font-size: 12px;">Admin</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <script>
                function fillDemoUser(userType) {
                    let email = '';
                    let password = '';
                    
                    switch(userType) {
                        case 'nurullah':
                            email = 'nurullah@nurullah.net';
                            password = 'test';
                            break;
                        case 'turkbilisim':
                            email = 'info@turkbilisim.com.tr';
                            password = 'test';
                            break;
                        case 'laravel':
                            email = 'laravel@test';
                            password = 'test';
                            break;
                        case 'a':
                            email = 'a@test';
                            password = 'test';
                            break;
                        case 'b':
                            email = 'b@test';
                            password = 'test';
                            break;
                        case 'c':
                            email = 'c@test';
                            password = 'test';
                            break;
                    }
                    
                    document.querySelector('input[name="email"]').value = email;
                    document.querySelector('input[name="password"]').value = password;
                }
            </script>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $attributes = $__attributesOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__attributesOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b)): ?>
<?php $component = $__componentOriginal69dc84650370d1d4dc1b42d016d7226b; ?>
<?php unset($__componentOriginal69dc84650370d1d4dc1b42d016d7226b); ?>
<?php endif; ?><?php /**PATH C:\laragon\www\laravel\resources\views/auth/login.blade.php ENDPATH**/ ?>