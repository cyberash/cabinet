			<div class="content-box">

				<div class="content-box-header">

					<h3>Notification Templates</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">

<!-- BEGIN: notfound -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								No notification templates. <a href="?object=addnotifytemplate">Create new</a>
							</div>
						</div>
<!-- END: notfound -->
<!-- BEGIN: table -->
						<table>

							<thead>
								<tr>
								   <th>Template #</th>
								   <th>Type</th>
								   <th>Subject</th>
								   <th>Language</th>
								   <th></th>

								</tr>

							</thead>

							<tfoot>
								<tr>
									<td colspan="6">
										<div class="bulk-actions align-left">
											<a class="button" href="?object=addnotifytemplate">Add Notification Template</a>
										</div>

										<div class="clear"></div>
									</td>
								</tr>
							</tfoot>

							<tbody>
<!-- BEGIN: row -->
								<tr>
									<td>{NT.id}</td>
									<td>{NT.type}</td>
									<td>{NT.subject}</td>
									<td>{NT.langcode}</td>
									<td>
									<a href="?object=editnotifytemplate&ntid={NT.id}" title="Edit"><img src="themes/simpla/images/hammer_screwdriver.png" alt="Edit"></a>
									<a href="?action=notifytemplate&ntid={NT.id}" title="Delete"><img src="themes/simpla/images/cross.png" alt="Delete"></a>
									</td>
								</tr>
<!-- END: row -->
							</tbody>
						</table>
<!-- END: table -->

					</div>

				</div>

			</div>
