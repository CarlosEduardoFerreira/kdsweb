<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ route('admin.dashboard') }}" class="site_title">
                <span>{{ config('app.name') }}</span>
            </a>
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="{{ auth()->user()->avatar }}" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <h2>{{ auth()->user()->username }}</h2>
            </div>
        </div>
        <!-- /menu profile quick info -->

        <br/>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>{{ __('views.backend.section.navigation.sub_header_0') }}</h3>
                <ul class="nav side-menu">
                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-home" aria-hidden="true"></i>
                            {{ __('views.backend.section.navigation.menu_0_1') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="menu_section">
                <h3>{{ __('views.backend.section.navigation.sub_header_1') }}</h3>
                <ul class="nav side-menu">
                		<?php if(auth()->user()->hasRole('administrator')) { ?>
                        <li>
                            <a href="{{ route('admin.resellers', ['adminId' => '0']) }}">
                                <i class="fa fa-briefcase" aria-hidden="true"></i>
                                {{ __('views.backend.section.navigation.menu_1_1') }}
                            </a>
                        </li>
                    <?php } ?>
                    <?php if(auth()->user()->hasRole('administrator') || auth()->user()->hasRole('reseller')) { ?>
                        <li>
                            <a href="{{ route('admin.storegroups', ['resellerId' => '0']) }}">
                                <i class="fa fa-sitemap" aria-hidden="true"></i>
                                {{ __('views.backend.section.navigation.menu_1_2') }}
                            </a>
                        </li>
                    <?php } ?>
                    <li>
                        <a href="{{ route('admin.stores', ['storegroupId' => '0']) }}">
                            <i class="fa fa-cutlery" aria-hidden="true"></i>
                            {{ __('views.backend.section.navigation.menu_1_3') }}
                        </a>
                    </li>
                    <!--
                    <li>
                        <a href="{{ route('admin.permissions') }}">
                            <i class="fa fa-key" aria-hidden="true"></i>
                            {{ __('views.backend.section.navigation.menu_1_2') }}
                        </a>
                    </li>
                    -->
                </ul>
            </div>

            <!--
            <div class="menu_section">
                <h3>{{ __('views.backend.section.navigation.sub_header_2') }}</h3>

                <ul class="nav side-menu">
                    <li>
                        <a>
                            <i class="fa fa-list"></i>
                            {{ __('views.backend.section.navigation.menu_2_1') }}
                            <span class="fa fa-chevron-down"></span>
                        </a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('log-viewer::dashboard') }}">
                                    {{ __('views.backend.section.navigation.menu_2_2') }}
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('log-viewer::logs.list') }}">
                                    {{ __('views.backend.section.navigation.menu_2_3') }}
                                </a>
                            </li>

                        </ul>
                    </li>
                </ul>
            </div>
            -->

            <div class="menu_section">
                <h3>{{ __('views.backend.section.navigation.sub_header_3') }}</h3>
                <ul class="nav side-menu">
                  <li>
                      <a href="http://bematechus.com" target="_blank" title="Bematech"><i class="fa fa-building" aria-hidden="true"></i>Bematech</a>
                  </li>
                  <li>
                      <a href="https://totvs.com" target="_blank" title="Totvs"><i class="fa fa-building-o" aria-hidden="true"></i>Totvs</a>
                  </li>
                </ul>
            </div>
        </div>
        <!-- /sidebar menu -->
    </div>
</div>
