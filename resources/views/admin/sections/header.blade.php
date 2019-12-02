<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>
            
            <style>
                .lbl-menu-top-right { padding-left:20px; cursor:pointer; font-weight:200; font-size:14px; }
            </style>

            <ul class="nav navbar-nav navbar-right">
                <li class="">
                
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                       aria-expanded="false">
                        <img src="{{ auth()->user()->avatar }}" alt="">{{ auth()->user()->name }}
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-usermenu pull-right">
						<li>
                            <a id="profile-link" href="">
                                <i class="fa fa-user pull-left" style="font-size:20px;"></i>
                                <label class="lbl-menu-top-right"> &nbsp;Profile</label>
                            </a>
                        	</li>

                            <li>
                            <a id="profile-link" href="{{ route('admin.agreement_page') }}">
                            <img src="/images/output-onlinepngtools.png" title="" style="margin:auto;height:20px;cursor:hand;">
                            <label class="lbl-menu-top-right"> &nbsp;Agreement</label>
                            </a>
                        	</li>
                            

                        	<li>
                            <a href="{{ route('logout') }}">
                                <i class="fa fa-sign-out pull-left" style="font-size:20px;"></i>
                                <label class="lbl-menu-top-right">{{ __('views.backend.section.header.menu_0') }}</label>
                            </a>
                        	</li>
                    </ul>
                    
                </li>
            </ul>
            
			<script>
            		var route = "{{ route('admin.stores.edit', [auth()->user()->id, 'filter' => false]) }}";
				var roleID = "{{ auth()->user()->roles()->first()->id }}";
				
				if (roleID == "3") {
					route = "{{ route('admin.storegroups.edit', [auth()->user()->id, 'filter' => false]) }}";
					
				} else if (roleID == "2") {
					route = "{{ route('admin.resellers.edit', [auth()->user()->id, 'filter' => false]) }}";
					
				}
				document.getElementById('profile-link').href = route;
            </script>
            
        </nav>
    </div>
</div>
