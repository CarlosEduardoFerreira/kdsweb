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
            
            <div class="menu_section" id="div-store-config" class="">
                <h3>{{ __('views.backend.section.navigation.sub_header_1') }}</h3>
                <ul class="nav side-menu">

                    	<?php if(auth()->user()->hasRole('administrator')) { ?>
                        <li>
                            <a href="{{ route('admin.resellers', ['adminId' => '0', 'filter' => false]) }}">
                                <i class="fa fa-briefcase" aria-hidden="true"></i>
                                {{ __('views.backend.section.navigation.menu_1_1') }}
                            </a>
                        </li>
                    <?php } ?>
    
                    <?php if(auth()->user()->hasRole('administrator') || auth()->user()->hasRole('reseller')) { ?>
                        <li>
                            <a href="{{ route('admin.storegroups', ['resellerId' => '0', 'filter' => false]) }}">
                                <i class="fa fa-sitemap" aria-hidden="true"></i>
                                {{ __('views.backend.section.navigation.menu_1_2') }}
                            </a>
                        </li>
                    <?php } ?>
                    
                    <?php if(auth()->user()->hasRole('administrator') || auth()->user()->hasRole('reseller')|| auth()->user()->hasRole('storegroup')) { ?>
                        <li>
                            <a href="{{ route('admin.stores', ['storegroupId' => '0', 'filter' => false]) }}">
                                <i class="fa fa-cutlery" aria-hidden="true"></i>
                                {{ __('views.backend.section.navigation.menu_1_3') }}
                            </a>
                        </li>
                    <?php } ?>
                    
                    <?php 
                        //$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                        
                        $selected = isset($selected) ? $selected : '';
                    
                        if(auth()->user()->hasRole('store')) { ?>
                        
                        <input type="hidden" id="selected-page" value="{{ $selected }}">
                        
                        <li id="li-store-config">
                            <a id="a-store-config" onclick="goToStoreConfig()">
                                <i class="fa fa-cogs" aria-hidden="true"></i>
                                Configuration
                            </a>
                            
                        </li>
                        <li id="li-store-reports">
                        		<a id="a-store-reports" onclick="goToStoreReports()">
                                <i class="fa fa-line-chart" aria-hidden="true"></i>
                                Reports
                            	</a>
                         </li>
                    <?php } ?>

                </ul>
            </div>
            
            <!-- do not remove this log -->
            <div id="log">&nbsp;</div>
            
            <script type="text/javascript">

            		function checkPage() {
					var selectedPage = document.getElementById("selected-page");
					if (selectedPage != null) {
        					var selectedValue = selectedPage.value;
            				document.getElementById("a-store-"+selectedValue).classList.remove("current-page");
					}
            		}

				checkPage();
				
            		function goToStoreConfig() {
					window.location.href = "{{ route('admin.stores.config', [auth()->user()->id, 'selected' => 'config']) }}";
            		}

            		function goToStoreReports() {
    					window.location.href = "{{ route('admin.stores.report', [auth()->user()->id, 'selected' => 'reports']) }}";
                	}

            </script>

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




