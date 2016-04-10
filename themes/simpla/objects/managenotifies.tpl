			<div class="content-box">

				<div class="content-box-header">

					<h3>List sent notifications</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: notfound -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								No notifications
							</div>
						</div>
<!-- END: notfound -->
<!-- BEGIN: table -->
						<table>

							<thead>
								<tr>
								   <th>Notify #</th>
								   <th>Subject</th>
								   <th>Customer</th>
								   <th>Module</th>
								   <th>Date</th>
								   <th>Status</th>
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
<!-- BEGIN: row -->
								<tr>
									<td>{NOT.id}</td>
									<td>{NOT.subject}</td>
									<td>{NOT.userid}</td>
									<td>{NOT.moduleid}</td>
									<td>{NOT.date}</td>
									<td>
									<a href="?object=editorder&orderid={ORDER.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit"></a>
									<a href="?action=delorder&orderid={ORDER.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete"></a>
									</td>
								</tr>
<!-- END: row -->
							</tbody>
						</table>
<!-- END: table -->

					</div>

				</div>

			</div>
