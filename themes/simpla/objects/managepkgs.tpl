			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.listproducts}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: pkgsinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.nopksfound}. {LANG.please}, <a href="?object=addpkg">{LANG.addnewpackage}</a>
							</div>
						</div>
<!-- END: pkgsinfo -->
<!-- BEGIN: pkgstable -->
						<table>

							<thead>
								<tr>

								   <th>{LANG.packagesharp}</th>
								   <th>{LANG.packagename}</th>
								   <th>{LANG.packagepreset}</th>
								   <th>{LANG.paymenttype}e</th>
								   <th>{LANG.stock}</th>
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
<!-- BEGIN: pkgrow -->
								<tr>
									<td>{PKG.id}</td>
									<td>{PKG.name}</td>
									<td>{PRESETNAME}</td>
									<td>{PKG.paytype}</td>
									<td>{PKG.stock}</td>
									<td>
									<a href="?object=editpkg&pkgid={PKG.id}" title="Edit Package"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit Package"></a>
									<a href="?action=delpkg&pkgid={PKG.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete"></a>
									</td>
								</tr>
<!-- END: pkgrow -->
							</tbody>
						</table>
<!-- END: pkgstable -->

					</div>

				</div>

			</div>
