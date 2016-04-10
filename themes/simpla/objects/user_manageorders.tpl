			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.yourorders}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: ordersinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noorders}. {LANG.please}, <a href="?object=addpkg">{LANG.newordercustomer}r</a>
							</div>
						</div>
<!-- END: ordersinfo -->
<!-- BEGIN: orderstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.ordersharp}</th>
								   <th>{LANG.package}</th>
								   <th>{LANG.lastinvoice}</th>
								   <th>{LANG.paycycle}</th>
								   <th>{LANG.recurprice}</th>
								   <th>{LANG.status}</th>
								   <th></th>


								</tr>

							</thead>

							<tbody>
<!-- BEGIN: ordersrow -->
								<tr>
									<td>{ORDER.id}</td>
									<td>{PKGNAME}</td>
									<td>{ORDER.lastinv}</td>
									<td>{ORDER.cycle}</td>
									<td>{ORDER.recuramount}</td>
									<td>{ORDER.status}</td>
									<td><a class="button" href="?object=vieworder&orderid={ORDER.orderid}">{LANG.overview}</a></td>
								</tr>
<!-- END: ordersrow -->
							</tbody>
						</table>
<!-- END: orderstable -->

					</div>

				</div>

			</div>
