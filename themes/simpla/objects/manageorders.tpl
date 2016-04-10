			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.listorders}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: ordersinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noorders}. {LANG.please}, <a href="?object=addorder">{LANG.createneworder}</a>
							</div>
						</div>
<!-- END: ordersinfo -->
<!-- BEGIN: orderstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.ordersharp}</th>
								   <th>{LANG.customer}</th>
								   <th>{LANG.package}</th>
								   <th>{LANG.paymenttype}</th>
								   <th>{LANG.duedate}</th>
								   <th></th>

								</tr>

							</thead>

							<tfoot>
								<tr>
									<td colspan="6">
										<div class="bulk-actions align-left">
											<a class="button" href="?object=addorder">Create new order</a>
										</div>
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
<!-- BEGIN: ordersrow -->
								<tr>
									<td>{ORDER.id}</td>
									<td>{USERNAME}</td>
									<td>{PKGNAME}</td>
									<td>{ORDER.cycle}</td>
									<td>{ORDER.nextdue}</td>
									<td>
									<a href="?object=editorder&orderid={ORDER.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit"></a>
									<a href="?action=delorder&orderid={ORDER.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete"></a>
									</td>
								</tr>
<!-- END: ordersrow -->
							</tbody>
						</table>
<!-- END: orderstable -->

					</div>

				</div>

			</div>
