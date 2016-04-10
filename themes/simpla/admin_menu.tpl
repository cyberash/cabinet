			<ul id="main-nav">  <!-- Accordion Menu -->

				<li>
					<a class="nav-top-item no-submenu {DASHCURR}" href="/"> <!-- Add the class "no-submenu" to menu items with no sub menu -->
					{LANG.dashboard}
					</a>
				</li>

				<li>
					<a href="#" class="nav-top-item {CUSTCURR}"> <!-- Add the class "current" to current menu item -->
					{LANG.customers}
					</a>
					<ul>
						<li><a href="?object=addcustomer" class="{CUSTCREATECURR}">{LANG.createnewcustomer}</a></li>
						<li><a href="?object=managecustomers" class="{CUSTMANAGECURR}">{LANG.listcostumers}</a></li>
					</ul>
				</li>

				<li>
					<a href="#" class="nav-top-item {SUPCURR}"> <!-- Add the class "current" to current menu item -->
					{LANG.support}
					</a>
					<ul>
						<li><a href="?object=managetickets&where=Support" class="{OPENTCURR}">{LANG.opentickets}</a></li>
						<li><a href="?object=managetickets&where=Customer" class="{ANSWTCURR}">{LANG.answeredtickets}</a></li>
						<li><a href="?object=managetickets&where=Hold" class="{HOLDTCURR}">{LANG.onholdtickets}</a></li>
						<li><a href="?object=managetickets&where=Closed" class="{CLOSEDTCURR}">{LANG.closedtickets}</a></li>
						<li><a href="?object=managetickets&where=Progress" class="{PRORESSTCURR}">{LANG.inprogresstickets}</a></li>
						<li><a href="?object=addcustomer" class="{CUSTCREATECURR}">{LANG.createnewticket}</a></li>
						<li><a href="?object=managedepartments" class="{DEPSCURR}">{LANG.supportdepartments}</a></li>
					</ul>
				</li>

				<li>
					<a href="#" class="nav-top-item {PRODUCTSCURR}" >
						{LANG.products}
					</a>
					<ul>
						<li><a href="?object=addpkg" class="{ADDPKGCURR}">{LANG.createnewproduct}</a></li>
						<li><a href="?object=managepkgs" class="{PKGMANAGECURR}">{LANG.listproducts}</a></li>
						<li><a href="?object=managepres" class="{PRESETMANAGECURR}">{LANG.presets}</a></li>
					</ul>
				</li>

				<li>
					<a href="#" class="nav-top-item {ORDERSCURR}">
						{LANG.orders}
					</a>

					<ul>
						<li><a href="?object=addorder" class="{ADDORDERSCURR}">{LANG.createneworder}</a></li>
						<li><a href="?object=manageorders" class="{MANAGEORDERSCURR}">{LANG.listorders}</a></li>
					</ul>
				</li>

				<li>
					<a href="#" class="nav-top-item {FINCURR}" >
						{LANG.finances}
					</a>

					<ul>
						<li><a href="?object=addinv" class="{ADDINVCURR}">{LANG.sendinvoice}</a></li>
						<li><a href="?object=managetrans" class="{MANAGETRANS}">{LANG.listtransactions}</a></li>
						<li><a href="?object=manageinvs" class="{MANAGEINVCURR}">{LANG.listinvoices}</a></li>
						<li><a href="?object=managegatewaymodules" class="{MANAGEGWMCURR}">{LANG.paymentgateways}</a></li>
						<li><a href="?object=managecurrencies" class="{MANAGECURRS}">{LANG.currencies}</a></li>
					</ul>
				</li>
				<li>
					<a href="#" class="nav-top-item {SERVERSCURR}">
						{LANG.servers}
					</a>
					<ul>
						<li><a href="?object=manageservermodules" class="{SMODMANAGECURR}">{LANG.servermodules}</a></li>
						<li><a href="?object=manageservergroups" class="{SGROUPMANAGECURR}">{LANG.servergroups}</a></li>
						<li><a href="?object=manageservers" class="{SSERVERMANAGECURR}">{LANG.listservers}</a></li>
					</ul>
				</li>
				<li>
					<a href="#" class="nav-top-item {NOTIFYCURR}">
						Notifications
					</a>
					<ul>
						<li><a href="?object=sendmessage" class="{SENDNOTIFCURR}">Send new</a></li>
						<li><a href="?object=managenotifies" class="{NOTIFYHISTCURR}">History</a></li>
						<li><a href="?object=managenotifymodules" class="{NTMODULESCURR}">Modules</a></li>
						<li><a href="?object=managenotifytemplates" class="{NTEMPLATESCURR}">{LANG.templates}</a></li>
						
					</ul>
				</li>
				<li>
					<a href="#" class="nav-top-item {SETCURR}">
						System {LANG.settings}
					</a>
					<ul>
						<li><a href="?object=generalsettings" class="{GENERALSETTINGS}">{LANG.generalsettings}</a></li>
						<li><a href="?object=editcron" class="{CRONCURR}">{LANG.cronsettings}</a></li>
						<li><a href="#">{LANG.yourprofile}</a></li>
					</ul>
				</li>
				<li>
					<a href="#" class="nav-top-item {PERSSETCURR}">
						{LANG.account}
					</a>
					<ul>
						<li><a href="?object=personalsettings" class="{PERSSETTINGSCURR}">{LANG.settings}</a></li>
						<li><a href="?object=editcron" class="{CRONCURR}">Contacts</a></li>
					</ul>
				</li>

			</ul> <!-- End #main-nav -->
