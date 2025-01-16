{{-- resources/views/admin/partials/header.blade.php --}}
<header class="navbar navbar-expand-md d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{ route('admin.dashboard') }}">
                <span class="navbar-brand-image"><i class="fa-thin fa-bee"></i>
            </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
            <!-- Color Picker -->
            <div class="color-mode theme-color-mode me-2">
                <div class="color-picker-container" style="position: relative;">
                    <div id="selectedColor" class="form-control form-control-color"
                        style="width: 20px; height: 20px; cursor: pointer;" onclick="toggleColorPicker()"></div>
                    <div id="colorPickerDropdown"
                        style="display: none; position: absolute; top: 100%; right: 0; width: 220px; background: var(--tblr-bg-surface); border: 1px solid var(--tblr-border-color); border-radius: 4px; padding: 10px; margin-top: 5px; z-index: 1000; box-shadow: 0 2px 10px rgba(0,0,0,0.1); display: flex; flex-wrap: wrap; gap: 5px;">
                        <!-- Kırmızı tonları -->
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FF7F7F')" data-color="#FF7F7F"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FF5252')" data-color="#FF5252"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#F44336')" data-color="#F44336"></div>

                        <!-- Turuncu tonları -->
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FFB74D')" data-color="#FFB74D"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FF9800')" data-color="#FF9800"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#F57C00')" data-color="#F57C00"></div>

                        <!-- Sarı tonları -->
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FFF176')" data-color="#FFF176"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FFEB3B')" data-color="#FFEB3B"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#FBC02D')" data-color="#FBC02D"></div>

                        <!-- Yeşil tonları -->
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#81C784')" data-color="#81C784"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#4CAF50')" data-color="#4CAF50"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#2E7D32')" data-color="#2E7D32"></div>

                        <!-- Turkuaz tonları -->
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#4DD0E1')" data-color="#4DD0E1"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#00BCD4')" data-color="#00BCD4"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#0097A7')" data-color="#0097A7"></div>

                        <!-- Mavi tonları -->
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#64B5F6')" data-color="#64B5F6"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#2196F3')" data-color="#2196F3"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#1976D2')" data-color="#1976D2"></div>

                        <!-- Mor tonları -->
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#BA68C8')" data-color="#BA68C8"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#9C27B0')" data-color="#9C27B0"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#7B1FA2')" data-color="#7B1FA2"></div>

                        <!-- Pembe tonları -->
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#F06292')" data-color="#F06292"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#E91E63')" data-color="#E91E63"></div>
                        <div class="color-option"
                            style="width: 20px; height: 20px; cursor: pointer; border-radius: 4px; border: 1px solid var(--tblr-border-color);"
                            onclick="changeColor('#C2185B')" data-color="#C2185B"></div>
                    </div>
                </div>
            </div>

            <div class="theme-mode mt-2">
                <input type="checkbox" id="switch" class="dark-switch">
                <div class="app">
                    <div class="switch-content">
                        <div class="switch-label"></div>
                        <label for="switch">
                            <div class="toggle"></div>
                            <div class="names">
                                <p class="light"><i class="fa-light fa-moon"></i></p>
                                <p class="dark"><i class="fa-light fa-brightness-low" style="margin-top: 6px;"></i></p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="d-none d-md-flex">
                <div class="nav-item dropdown d-none d-md-flex me-3">
                    <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
                        aria-label="Show notifications">
                        <i class="fa-regular fa-bell"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Last updates</h3>
                            </div>
                            <div class="list-group list-group-flush list-group-hoverable">
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span
                                                class="status-dot status-dot-animated bg-red d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="#" class="text-body d-block">Example 1</a>
                                            <div class="d-block text-muted text-truncate mt-n1">
                                                Change deprecated html tags to text decoration classes (#29604)
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="list-group-item-actions">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span class="status-dot d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="#" class="text-body d-block">Example 2</a>
                                            <div class="d-block text-muted text-truncate mt-n1">
                                                justify-content:between ⇒ justify-content:space-between (#29734)
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="list-group-item-actions show">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span class="status-dot d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="#" class="text-body d-block">Example 3</a>
                                            <div class="d-block text-muted text-truncate mt-n1">
                                                Update change-version.js (#29736)
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="list-group-item-actions">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span
                                                class="status-dot status-dot-animated bg-green d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="#" class="text-body d-block">Example 4</a>
                                            <div class="d-block text-muted text-truncate mt-n1">
                                                Regenerate package-lock.json (#29730)
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="list-group-item-actions">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav-item dropdown">
                {{-- header.blade.php --}}
                <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <span class="avatar avatar-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24"
                            height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                            <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                        </svg>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <a class="dropdown-item" href=""><svg xmlns="http://www.w3.org/2000/svg"
                            class="icon icon-tabler icon-tabler-edit mr-1" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                            <path d="M16 5l3 3" />
                        </svg> {{ auth()->user()->name }}</a>
                    <a class="dropdown-item" href=""><svg xmlns="http://www.w3.org/2000/svg"
                            class="icon icon-tabler icon-tabler-activity mr-1" width="24" height="24"
                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 12h4l3 8l4 -16l3 8h4" />
                        </svg> {{ __('admin.my_logs') }}</a>
                    <hr class="dropdown-divider">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" class="dropdown-item"
                            style="padding-left: 16px !important; padding-bottom: 12px !important;" onclick="event.preventDefault();
                                 this.closest('form').submit();"><svg xmlns="http://www.w3.org/2000/svg"
                                class="icon icon-tabler icon-tabler-logout  mr-1" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                <path d="M9 12h12l-3 -3" />
                                <path d="M18 15l3 -3" />
                            </svg>
                            {{ __('admin.logout') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                @include('admin.partials.navbar')
            </div>
        </div>
    </div>
</header>