			<ul id="main-nav">  <!-- Accordion Menu -->

				<li>
					<a class="nav-top-item no-submenu {DASHCURR}" href="/" > <!-- Add the class "no-submenu" to menu items with no sub menu -->
					{LANG.dashboard}
					</a>
				</li>

				<li>
					<a href="#" class="nav-top-item {FINCURR}"> <!-- Add the class "current" to current menu item -->
					{LANG.finances}
					</a>
					<ul>
						<li><a href="?object=manageinvs" class="{INVSCURR}">{LANG.yourinvoices}</a></li>
						<li><a href="#">{LANG.lastpayments}</a></li>
					</ul>
				</li>

				<li>
					<a href="#" class="nav-top-item {ORDERSCURR}">
						{LANG.orders}
					</a>

					<ul>
						<li><a href="?object=addorder" class="{ADDORDERSCURR}">{LANG.placenewcustorder}</a></li>
						<li><a href="?object=manageorders" class="{MANAGEORDERSCURR}">{LANG.listorders}</a></li>
					</ul>
				</li>

				<li>
					<a href="#" class="nav-top-item {SUPPCURR}">
						{LANG.support}
					</a>

					<ul>
						<li><a href="?object=managetickets" class="{TICKETSSCURR}">{LANG.tickets}</a></li>
						<li><a href="?object=addticket" class="{NEWTCURR}">{LANG.newticket}</a></li>
					</ul>
				</li>

				<li>
					<a href="#" class="nav-top-item {SETCURR}">
						{LANG.settings}
					</a>
					<ul>
						<li><a href="?object=editprofile" class="{CURRPROFILE}">{LANG.contactprofile}</a></li>
						<li><a href="?object=perssystemsettings" class="{SYSTEMCURRSETT}">System Settings</a></li>
					</ul>
				</li>

			</ul> <!-- End #main-nav -->
