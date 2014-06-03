<style>
	@media (min-width: 979px) {
		.wekeke{
			 margin-top: -2px !important;
			 width:100%;
			 position:fixed;
		}
		.navbar-inner {
			 border: 0px !important;
			 border-radius: 0px !important;
		}
	}
	.nav-tabs {
		margin-bottom: 6px;
	}
	.content {
		padding-top: 45px;
	}
</style>

<div class="navbar navbar-inverse wekeke" style="z-index:1029; ">
    <div class="navbar-inner">
        <div class="container-fluid">
 		
			<?if(is_login()) :?>
            <div class="nav-collapse collapse">
                <ul class="nav">
                    <li <?echo $current=='beranda' ? 'class="active"' : '';?>><a class="brand" href="<?=active_module_url();?>"><?=strtoupper(active_module());?></a></li>
                    <li class="dropdown <?echo $current=='pengaturan' ? 'active' : '';?>">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Pengaturan <strong class="caret"></strong></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?=active_module_url('apps');?>">Aplikasi</a></li>
                            <li class="nav-header">User &amp; Privileges</li>
                            <li><a href="<?=active_module_url('users');?>">Users</a></li>
                            <li><a href="<?=active_module_url('groups');?>">Group Users</a></li>
                            <li><a href="<?=active_module_url('privileges');?>">Group Privileges</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
			<? endif; ?>
        </div>
    </div>
</div>