<?php
return array(
	'default' => '<reference name="left">										
					<action method="unsetChild"><name>tags_popular</name></action>
				</reference>
				<reference name="right">
					<action method="unsetChild"><name>right.poll</name></action>
                    <action method="unsetChild"><name>wishlist</name></action>
                    <action method="unsetChild"><name>catalog.compare.sidebar</name></action>
				</reference>',
	'catalog_category_layered' => '<reference name="left">
									   <remove name="catalog.leftnav" />
									   <remove name="em.catalog.leftnav" />
									</reference>',
	'catalog_category_default' => '<reference name="left">
									   <action method="unsetChild"><name>catalog.leftnav</name></action>
									</reference>',
	'catalog_product_view' => '<reference name="right">
								   <action method="unsetChild"><name>catalog.product.related</name></action>
								</reference>',
	'catalogsearch_result_index' => '<reference name="left">
										<remove name="catalogsearch.leftnav" />
										<remove name="em.catalog.leftnav" />
									</reference>',
	'customer_account' => '<reference name="left">
								<remove name="sale.reorder.sidebar" />
							</reference>',
	'customer_logged_in' => '<reference name="right">
								<remove name="sale.reorder.sidebar" />
							</reference>'							
);
?>