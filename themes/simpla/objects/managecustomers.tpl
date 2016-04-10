			<div class="content-box"><!-- Start Content Box -->

				<div class="content-box-header">

					<h3>{LANG.listcostumers}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab" id="tab1"> <!-- This is the target div. id must match the href of this div's tab -->

<!-- BEGIN: usersinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noactiveusers}
							</div>
						</div>
<!-- END: usersinfo -->
<!-- BEGIN: userstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.usersharp}</th>
								   <th>{LANG.username}</th>
								   <th>{LANG.email}</th>
								   <th>{LANG.dateregistered}</th>
								   <th>{LANG.lastlogin}</th>
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
<!-- BEGIN: userrow -->
								<tr>
									<td><a href="?object=managecustomer&userid={USER.id}">{USER.id}</a></td>
									<td><a href="?object=managecustomer&username={USER.username}">{USER.username}</a></td>
									<td>{USER.email}</td>
									<td>{USER.opentime}</td>
									<td>{USER.lastlogin}</td>
									<td>{USER.status}</td>
									<td>
										<a href="?object=managecustomer&userid={USER.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit" /></a>
										<a href="?action=delcustomer&userid={USER.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete" /></a>
										 
									</td>
								</tr>
<!-- END: userrow -->
							</tbody>
						</table>
<!-- END: userstable -->

					</div> <!-- End #tab1 -->

				</div> <!-- End .content-box-content -->

			</div> <!-- End .content-box -->
