			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.supportdepartments}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: depsinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.nodeps}. {LANG.please}, <a href="?object=adddep">{LANG.cretenewdep}</a>
							</div>
						</div>
<!-- END: depsinfo -->
<!-- BEGIN: depstable -->
						<table>

							<thead>
								<tr>

								   <th>{LANG.departmentsharp}</th>
								   <th>{LANG.departmentname}</th>
								   <th>{LANG.type}</th>
								   <th></th>

								</tr>

							</thead>

							<tfoot>
								<tr>
									<td colspan="6">
										<div class="bulk-actions align-left">
											<a class="button" href="?object=adddep">{LANG.cretenewdep}</a>
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
<!-- BEGIN: deprow -->
								<tr>
									<td>{DEP.id}</td>
									<td>{DEP.name}</td>
									<td>{DEP.type}</td>
									<td>
									<a href="?object=editdep&id={DEP.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit"></a>
									<a href="?action=deldep&id={DEP.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete"></a>
									</td>
								</tr>
<!-- END: deprow -->
							</tbody>
						</table>
<!-- END: depstable -->

					</div>

				</div>

			</div>
