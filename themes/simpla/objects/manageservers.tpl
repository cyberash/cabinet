			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.listservers}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: serversinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noservers}. {LANG.please}, <a href="?object=addserverstep1">add new server</a>
							</div>
						</div>
<!-- END: serversinfo -->
<!-- BEGIN: serverstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.serversharp}</th>
								   <th>{LANG.servername}</th>
								   <th>{LANG.groupname}</th>
								   <th>{LANG.modulename}</th>
								   <th>{LANG.status}</th>
								   <th></th>

								</tr>

							</thead>

							<tfoot>
								<tr>
									<td colspan="6">
										<div class="bulk-actions align-left">
											<a class="button" href="?object=addserverstep1">Add new server</a>
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
<!-- BEGIN: serversrow -->
								<tr>
									<td>{SERVER.ServerID}</td>
									<td>{SERVER.servername}</td>
									<td>{GROUPNAME}</td>
									<td>{MODULENAME}</td>
									<td>{SERVER.status}</td>
									<td>
										 <a href="?object=editserver&serverid={SERVER.ServerID}" title="Edit Server"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit Server"></a>
										 <a href="?action=delserver&id={SERVER.ServerID}" title="Delete Server"><img src="themes/simpla/images/cross.png" alt="Delete Server"></a>
									</td>
								</tr>
<!-- END: serversrow -->
							</tbody>
						</table>
<!-- END: serverstable -->

					</div>

				</div>

			</div>
