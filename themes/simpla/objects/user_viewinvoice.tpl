<!-- BEGIN: inverror -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.invoiceidisnotspecified}
							</div>
						</div>
<!-- END: inverror -->
<!-- BEGIN: invrights -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.youhavenorightstoviewinvoice}
							</div>
						</div>
<!-- END: invrights -->
<!-- BEGIN: invoice -->
			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.overinvoice} #{INV.id}</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form>
<!-- BEGIN: paid -->
					<fieldset class="column-right">
						<p>
							<label>{LANG.datepaid}:</label>
							{INV.datepaid}
							<label>{LANG.transaction}:</label>
							#{INV.transactionid}
						</p>
						
					</fieldset>
<!-- END: paid -->					
					<fieldset>
						<p>
							<label>{LANG.order}:</label>
							{LANG.ordersharp}<a href="?object=vieworder&orderid={ORDER.orderid}">{ORDER.orderid}</a> {LANG.forpkg} {PKG.name}
						</p>
						<p>
							<label>{LANG.datecreated}:</label>
							{INV.datecreated}
						</p>
						<p>
							<label>{LANG.duedate}:</label>
							{INV.datedue}
						</p>
						<p>
							<label>{LANG.duedate}:</label>
							{INV.status}
						</p>
						<p>
							<label>{LANG.amount}:</label>
							{INV.amount}
						</p>
						<p>
							<a class="button" href="/invoice.php?id={INV.invoiceid}">{LANG.gotopaymentspage}</a>
						</p>
					</fieldset>
					</form>

					</div>
				</div>

			</div>
<!-- END: invoice -->
