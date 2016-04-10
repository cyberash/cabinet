			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.presets}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: presetsinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.nopresets}. {LANG.please}, <a href="?object=addprestep1">{LANG.addnewpreset}</a>
							</div>
						</div>
<!-- END: presetsinfo -->
<!-- BEGIN: pkgstable -->
						<table>

							<thead>
								<tr>

								   <th>{LANG.presetsharp}</th>
								   <th>{LANG.presetname}</th>
								   <th>{LANG.servergroup}</th>
								   <th>{LANG.status}</th>
								   <th></th>

								</tr>

							</thead>
							<tfoot>
								<tr>
									<td colspan="6">
										<div class="bulk-actions align-left">
											<a class="button" href="?object=addprestep1">Create new preset</a>
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
<!-- BEGIN: pkgrow -->
								<tr>
									<td>{PKG.id}</td>
									<td>{PKG.name}</td>
									<td>{PKG.groupid}</td>
									<td>{PKG.status}</td>
									<td>
										<a href="?object=editpreset&presetid={PKG.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit" /></a>
										<a href="?action=delpres&presid={PKG.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete" /></a>
										 
									</td>
								</tr>
<!-- END: pkgrow -->
							</tbody>
						</table>
<!-- END: pkgstable -->

					</div>

				</div>

			</div>
