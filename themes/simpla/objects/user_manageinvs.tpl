			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.yourinvoices}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: invsinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noinvoices}
							</div>
						</div>
<!-- END: invsinfo -->
<!-- BEGIN: invstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.invoicesharp}</th>
								   <th>{LANG.order}</th>
								   <th>{LANG.amount}</th>
								   <th>{LANG.duedate}</th>
								   <th>{LANG.status}</th>
								   <th></th>
								   <th></th>
								</tr>

							</thead>

							<tbody>
<!-- BEGIN: invrow -->
								<tr>
									<td>{INV.id}</td>
									<td>{INV.orderid}</td>
									<td>{INV.amount}</td>
									<td>{INV.datedue}</td>
									<td>{INV.status}</td>
									<td><a class="button" href="?object=viewinvoice&invid={INV.invoiceid}">{LANG.overview}</a></td>
									<td><a class="button" href="invoice.php?invoiceid={INV.invoiceid}">{LANG.pay}</a></td>
								</tr>
<!-- END: invrow -->
							</tbody>
						</table>
<!-- END: invstable -->

					</div>

				</div>

			</div>
