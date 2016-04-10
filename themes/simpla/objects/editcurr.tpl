<!-- BEGIN: currinfo -->
						<div class="notification attention png_bg">
							<a href="#" class="close"><img src="themes/simpla/images/cross_grey_small.png" title="Close this notification" alt="close" /></a>
							<div>
								{LANG.curridisntspecified}
							</div>
						</div>
<!-- END: currinfo -->
<!-- BEGIN: currbox -->
			<div class="content-box">

				<div class="content-box-header">

					<h3>{LANG.editcurrency}</h3>

					<div class="clear"></div>

				</div> <!-- End .content-box-header -->

				<div class="content-box-content">

					<div class="tab-content default-tab">
					<form action="{PHP._SERVER.PHP_SELF}" method="POST">
					<input type="hidden" name="action" value="updatecurr">
					<input type="hidden" name="curid" value="{CURR.id}">
					<fieldset>
						<p>
							<label>{LANG.currencyname}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="name" value ="{CURR.name}"/>
						</p>
						<p>
							<label>{LANG.currencysymbol}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="symbol" value ="{CURR.symbol}"/>
						</p>
						<p>
							<label>{LANG.currencyfullname}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="desc" value ="{CURR.desc}"/>
						</p>
						<p>
							<label>{LANG.currencyrate}:</label>
							<input class="text-input small-input" type="text" id="small-input" name="rate" value ="{CURR.rate}"/>
						</p>
						<p>
							<input class="button" type="submit" value="Submit" {DISABLED}/>
						</p>
					</fieldset>
					</form>
					</div>

				</div>

			</div>
<!-- END: currbox -->
