			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.orderaccepted}</h3>
					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content"> <!-- Start .content-box-content -->

					<div class="tab-content default-tab" id="default">
					<p>{LANG.ordersharp}{ORDER.id} {LANG.forpkg} {PKG.name} {LANG.accepted}.</p>
					<p>{LANG.generatednewinv} #{INV.id} {LANG.invoiceon} {INV.amount}.</p>
					<p>
						<a class="button" href="invoice.php?id={INV.id}">{LANG.gotopaymentpage}</a>
					</p>
					
					</div>
					
				</div> <!-- End .content-box-content -->

			</div><!-- End .content-box -->
