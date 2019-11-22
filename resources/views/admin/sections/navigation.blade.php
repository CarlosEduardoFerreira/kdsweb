
<?php 
    $adm = auth()->user()->hasRole('administrator'); 
    $res = auth()->user()->hasRole('reseller');
    $stg = auth()->user()->hasRole('storegroup');
?>

<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
    
        <div style="text-align:center;margin-top:20px;margin-bottom:20px;">
            <a href="{{ route('admin.dashboard') }}">
                <!-- <span>{{ config('app.name') }}</span> -->
                <img id="kds_logo" src="{{ URL::to('/kds_logo.png') }}"/>
            </a>
        </div>
        <style>
            #kds_logo { width:80%; padding:20px; background:#fff; border-radius:10px; }
            .nav-sm #kds_logo { width:60px; padding:7px; }
        </style>

        <!-- menu profile quick info -->
        <!--
        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="{{ auth()->user()->avatar }}" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <h2>{{ auth()->user()->username }}</h2>
            </div>
        </div>
        -->
        <!-- /menu profile quick info -->

        <br/>

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
        
            <div class="menu_section">
                <h3>{{ __('views.backend.section.navigation.sub_header_0') }}</h3>
                <ul class="nav side-menu">
                <li>
                        <a href="{{ route('admin.settings') }}">
                            <i class="fa fa-wrench" aria-hidden="true"></i>
                            Settings
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="fa fa-home" aria-hidden="true"></i>
                            {{ __('views.backend.section.navigation.menu_0_1') }}
                        </a>
                    </li>

                    <?php if ($adm) { ?>
                        <li>
                        <a href="{{ route('admin.reports') }}">
                            <i class="fa fa-file-text" aria-hidden="true"></i>
                            Billing
                        </a>
                    </li>
                    <?php } ?>

                    <?php 
                        if(auth()->user()->hasRole('store')) { ?>
                            <li id="li-store-config" class="">
                                <a href="{{ route('admin.stores.config', [auth()->user()->id, 'link' => 'store-config']) }}">
                                    <i class="fa fa-cogs" aria-hidden="true"></i>
                                    Configuration
                                </a>
                            </li>

                            <li id="li-store-reports" class="">
                                <a href="{{ route('admin.stores.report', [auth()->user()->id, 'link' => 'store-reports']) }}">
                                    <i class="fa fa-line-chart" aria-hidden="true"></i>
                                    Reports
                                </a>
                            </li>
                    <?php } ?>

                </ul>
            </div>
            
            <?php if (($adm) || ($res) || ($stg)) { ?>
            <div class="menu_section" id="div-store-config">
                <h3>{{ __('views.backend.section.navigation.sub_header_1') }}</h3>
                <ul class="nav side-menu">

                    	<?php if($adm) { ?>
                        <li id="li-reseller" class="">
                            <a href="{{ route('admin.resellers', ['adminId' => '0', 'filter' => false]) }}">
                                <i class="fa fa-briefcase" aria-hidden="true"></i>
                                {{ __('views.backend.section.navigation.menu_1_1') }}
                            </a>
                        </li>
                    <?php } ?>
    
                    <?php if($adm || $res) { ?>
                        <li id="li-storegroup" class="">
                            <a href="{{ route('admin.storegroups', ['resellerId' => '0', 'filter' => false]) }}">
                                <i class="fa fa-sitemap" aria-hidden="true"></i>
                                {{ __('views.backend.section.navigation.menu_1_2') }}
                            </a>
                        </li>
                    <?php } ?>
                    
                    <?php if($adm || $res || $stg) { ?>
                        <li id="li-store" class="">
                            <a href="{{ route('admin.stores', ['storegroupId' => '0', 'filter' => false]) }}">
                                <i class="fa fa-cutlery" aria-hidden="true"></i>
                                {{ __('views.backend.section.navigation.menu_1_3') }}
                            </a>
                        </li>
                    <?php } ?>

                    <!-- 
                    	<li id="li-users" class="">
                    		<a href="{{ route('admin.users', [auth()->user()->id]) }}">
                            <i class="fa fa-users" aria-hidden="true"></i>
                            Users
                        	</a>
                     </li>
                      -->
                </ul>
                
                {{-- Set CSS to actual menu li ------------------------------- --}}
                <?php 
                $link = isset($link) ? $link : (isset($obj) ? $obj : '');
                ?>
                <input type="hidden" id="menu-link" value="{{ $link }}">
                {{-- ------------------------------- Set CSS to actual menu li --}}
                
            </div>
            <?php } ?>
            
            <!-- do not remove this log -->
            <div id="log">&nbsp;</div>

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


@section('styles')
    @parent
    
	<style>
        .main_menu_side .menu_section li a { font-size:16px; font-weight:200; }
        .side-menu li.hover { border-right: 5px solid #1ABB9C; background: rgba(255, 255, 255, 0.02); }
        .side-menu li.selected  { border-right: 5px solid #1ABB9C; background: rgba(255, 255, 255, 0.05); }
    </style>
@endsection

@section('scripts')
    @parent
    
    <script type="text/javascript">

		$(function(){
			
			// Hover and Click Menu li
            	$('.side-menu').find('li').each(function(){
            		$(this).hover(function(){
            				$(this).addClass('hover');
            			},function(){
            				$(this).removeClass('hover');
            			}
            		);
            	});

            	function setMenuCSS() {
                	var selectedPage = $("#menu-link").val();
                	if(selectedPage != '') {
                		$("#li-"+selectedPage).addClass('selected');
                	}
            	}
            
            	setMenuCSS();
                
		}); // $(function(){
		
    		

    </script>
@endsection







