			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.servergroups}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: servergroupsinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.noserversgroupfound}. {LANG.please}, <a href="?object=addservergroup">{LANG.addnewgroupofservers}</a>
							</div>
						</div>
<!-- END: servergroupsinfo -->
<!-- BEGIN: servergroupstable -->
						<table>

							<thead>
								<tr>
								   <th>{LANG.groupsharp}</th>
								   <th>{LANG.groupname}</th>
								   <th>{LANG.groupmodule}</th>
								   <th>{LANG.status}</th>
								   <th></th>

								</tr>

							</thead>

							<tfoot>
								<tr>
									<td colspan="6">
										<div class="bulk-actions align-left">
											<a class="button" href="?object=addservergroup">Add new group</a>
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
<!-- BEGIN: servergroupsrow -->
								<tr>
									<td>{GROUP.id}</td>
									<td>{GROUP.name}</td>
									<td>{MODULENAME}</td>
									<td>{GROUP.status}</td>
									<td>
									<a href="?object=editservergroup&id={GROUP.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit"></a>
									<a href="?action=delservergroup&id={GROUP.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete"></a>
									</td>
								</tr>
<!-- END: servergroupsrow -->
							</tbody>
						</table>
<!-- END: servergroupstable -->

					</div>

				</div>

			</div>
