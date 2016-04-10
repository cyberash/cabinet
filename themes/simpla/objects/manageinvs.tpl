			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.listinvoices}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: invsinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noinvoices}. {LANG.please}, <a href="?object=addpkg">{LANG.sendinvoice}</a>
							</div>
						</div>
<!-- END: invsinfo -->
<!-- BEGIN: invstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.invoicesharp}</th>
								   <th>{LANG.customer}</th>
								   <th>{LANG.ordersharp}</th>
								   <th>{LANG.amount}</th>
								   <th>{LANG.duedate}</th>
								   <th>{LANG.status}</th>
								   <th></th>
								</tr>

							</thead>

							<tfoot>
								<tr>
									<td colspan="6">
										<div class="pagination">
											<a href="#" title="First Page">&laquo; First</a><a href="#" title="Previous Page">&laquo; Previous</a>
<!-- BEGIN: page -->
											<a href="{LINK}" class="number {CURRENT}" title="{NUM}">{NUM}</a>
<!-- END: page -->
											<a href="#" title="Next Page">Next &raquo;</a><a href="#" title="Last Page">Last &raquo;</a>
										</div> <!-- End .pagination -->
										<div class="clear"></div>
									</td>
								</tr>
							</tfoot>

							<tbody>
<!-- BEGIN: invrow -->
								<tr>
									<td>{INV.id}</td>
									<td>{USERNAME}</td>
									<td>{INV.orderid}</td>
									<td>{AMOUNT}</td>
									<td>{INV.datedue}</td>
									<td>{INV.status}</td>
									<form action="{PHP._SERVER.PHP_SELF}" method="post">
									<input type="hidden" name="invid" value="{INV.id}">
									<input type="hidden" name="object" value="editinv">
									<td><input class="button" type="submit" value="Edit"></td>
									</form>
								</tr>
<!-- END: invrow -->
							</tbody>
						</table>
<!-- END: invstable -->

					</div>

				</div>

			</div>
