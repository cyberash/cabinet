<!-- BEGIN: ordererror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.orderidnotspecified}
							</div>
						</div>
<!-- END: ordererror -->
<!-- BEGIN: orderrights -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.youhavenorightstovieworder}
							</div>
						</div>
<!-- END: orderrights -->
<!-- BEGIN: order -->
			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.overvieworder} #{ORDER.orderid}</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="default">


					<form>
					<fieldset>
						<p>
							<label>{LANG.package}:</label>
							{PKGNAME}
						</p>
						<p>
							<label>{LANG.lastinvoice}:</label>
							#<a href="?object=viewinvoice&invid={LASTINV.invoiceid}">{LASTINV.invoiceid}</a>, {LASTINV.status}
						</p>
						<p>
							<label>{LANG.status}:</label>
							{ORDER.status}
						</p>
						<p>
							<label>{LANG.paycycle}:</label>
							{LANG.eachcycle} {ORDER.cycle} {LANG.cyclemonths}
						</p>
						<p>
							<label>{LANG.dateordered}:</label>
							<input class="text-input small-input" type="text" id="datecreated" name="orderdate" value="{ORDER.orderdate}" />
						</p>
						<p>
							<label>{LANG.nextdue}:</label>
							<input class="text-input small-input" type="text" id="datecreated" name="nextdue" value="{ORDER.nextdue}" />
						</p>
						<p>
							<label>{LANG.firstamount}:</label>
							<input class="text-input small-input" type="text" id="datecreated" name="firstamount" value="{ORDER.firstamount}" />
						</p>
						<p>
							<label>{LANG.recuramount}:</label>
							<input class="text-input small-input" type="text" id="datecreated" name="recuramount" value="{ORDER.recuramount}" />
						</p>

					</fieldset>
					</form>

					</div>
				</div>

			</div>
<!-- END: order -->
