<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:strip-space elements="a" />
	<xsl:output method="html" encoding="UTF-8" indent="no" />
	<xsl:template match="menus">
		<ul>
			<li>
				<label for="checkbox_menu">
					Menu
				</label>
			</li>
			<xsl:for-each select="menu">
				<li>
					<a>
						<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
						<xsl:value-of select="name" />
					</a>
				</li>
			</xsl:for-each>
			<xsl:if test="/response/result/site/admin=1">
				<li>
					<a href="/admin/logout">Logout</a>
				</li>
			</xsl:if>
		</ul>
	</xsl:template>
	<xsl:template match="navigation">
		<xsl:if test="next | previous">
			<nav>
				<xsl:if test="string-length(previous)">
					<a>
						<xsl:attribute name="href"><xsl:value-of select="previous" /></xsl:attribute>
						<xsl:attribute name="rel">previous</xsl:attribute>
						Newer
					</a>
				</xsl:if>
				<xsl:if test="string-length(next)">
					<a>
						<xsl:attribute name="href"><xsl:value-of select="next" /></xsl:attribute>
						<xsl:attribute name="rel">next</xsl:attribute>
						Older
					</a>
				</xsl:if>
				<hr />
			</nav>
		</xsl:if>
		<footer>
			<xsl:apply-templates select="menus" />
			<hr />
		</footer>
	</xsl:template>
	<xsl:template match="site">
		<header class="h-card">
			<h2>
				<xsl:apply-templates select="images/image" />
			</h2>
			<hgroup>
				<h1>
					<xsl:attribute name="class">p-name</xsl:attribute>
					<a>
						<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
						<xsl:attribute name="class">u-url</xsl:attribute>
						<xsl:attribute name="rel">author me</xsl:attribute>
						<xsl:value-of select="name" />
					</a>
					<xsl:if test="/response/result/site/admin=1">
						<sup>
							<a href="/admin/addpost">+post</a>
						</sup>
					</xsl:if>
				</h1>
				<h2>
					<xsl:attribute name="class">p-note</xsl:attribute>
					<xsl:value-of select="heading" />
				</h2>
			</hgroup>
			<xsl:apply-templates select="navigation/menus" />
			<hr />
		</header>
	</xsl:template>
	<xsl:template match="heading">
		<xsl:choose>
			<xsl:when test="/response/result/site/admin=1">
				<form>
					<xsl:attribute name="action">/admin/updateheading/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
					<xsl:attribute name="method">POST</xsl:attribute>
					<input>
						<xsl:attribute name="type">text</xsl:attribute>
						<xsl:attribute name="name">content</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="content" /></xsl:attribute>
					</input>
					<input type="submit" value="Save" />
				</form>
				<sup>
					<a>
						<xsl:attribute name="href">/admin/upcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						@up
					</a>
					<a>
						<xsl:attribute name="href">/admin/downcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						@down
					</a>
					<a>
						<xsl:attribute name="href">/admin/deletecomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						-delete
					</a>
				</sup>
			</xsl:when>
			<xsl:otherwise>
				<h4>
					<xsl:value-of select="content" />
				</h4>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="p">
		<xsl:choose>
			<xsl:when test="/response/result/site/admin=1">
				<form>
					<xsl:attribute name="action">/admin/updatep/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
					<xsl:attribute name="method">POST</xsl:attribute>
					<textarea>
						<xsl:attribute name="name">span</xsl:attribute>
						<xsl:if test="summary and summary != 'false' and summary != 0">
							<xsl:attribute name="class">p-summary</xsl:attribute>
						</xsl:if>
						<xsl:value-of select="span" />
					</textarea>
					<input type="submit" value="Save" />
				</form>
				<sup>
					<a>
						<xsl:attribute name="href">/admin/upcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						@up
					</a>
					<a>
						<xsl:attribute name="href">/admin/downcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						@down
					</a>
					<a>
						<xsl:attribute name="href">/admin/summaryp/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						@summary
					</a>					
					<a>
						<xsl:attribute name="href">/admin/deletecomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						-delete
					</a>
				</sup>
			</xsl:when>
			<xsl:otherwise>
				<p>
					<xsl:if test="summary and summary != 'false' and summary != 0">
						<xsl:attribute name="class">p-summary</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="span" disable-output-escaping="yes" />
					<xsl:if test="summary and summary != 'false' and summary != 0">
						<a>
							<xsl:attribute name="class">readmore</xsl:attribute>
							<xsl:attribute name="href"><xsl:value-of select="../../../../url" /></xsl:attribute>
							read more...
						</a>
					</xsl:if>
				</p>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="li">
		<li>
			<xsl:choose>
				<xsl:when test="/response/result/site/admin=1">
					<form>
						<xsl:attribute name="action">/admin/updateli/<xsl:value-of select="../../../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						<xsl:attribute name="method">POST</xsl:attribute>
						<input>
							<xsl:attribute name="type">text</xsl:attribute>
							<xsl:attribute name="name">content</xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="content" /></xsl:attribute>
							<xsl:choose>
								<xsl:when test="strikeout=1">
									<xsl:attribute name="class">strikeout</xsl:attribute>
								</xsl:when>
							</xsl:choose>
						</input>
						<input type="submit" value="Save" />
					</form>
					<sup>
						<a>
							<xsl:attribute name="href">/admin/upli/<xsl:value-of select="../../../../../../site_post_ID" />/<xsl:value-of select="ul_li_ID" /></xsl:attribute>
							@up
						</a>
						<a>
							<xsl:attribute name="href">/admin/downli/<xsl:value-of select="../../../../../../site_post_ID" />/<xsl:value-of select="ul_li_ID" /></xsl:attribute>
							@down
						</a>
						<a>
							<xsl:attribute name="href">/admin/strikeli/<xsl:value-of select="../../../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
							@strike
						</a>
						<a>
							<xsl:attribute name="href">/admin/deleteli/<xsl:value-of select="../../../../../../site_post_ID" />/<xsl:value-of select="ul_li_ID" /></xsl:attribute>
							-delete
						</a>					
					</sup>
				</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="strikeout=1">
							<strike><xsl:value-of select="content" /></strike>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="content" />
						</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</li>
	</xsl:template>
	<xsl:template match="ul">
		<ul>
			<xsl:apply-templates select="items/li" />
		</ul>
		<xsl:if test="/response/result/site/admin=1">
			<sup>
				<a>
					<xsl:attribute name="href">/admin/addli/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
					+li
				</a>
				<a>
					<xsl:attribute name="href">/admin/upcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
					@up
				</a>
				<a>
					<xsl:attribute name="href">/admin/downcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
					@down
				</a>
				<a>
					<xsl:attribute name="href">/admin/deletecomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
					-delete
				</a>
			</sup>
		</xsl:if>
	</xsl:template>
	<xsl:template match="ol">
		<ol>
			<xsl:apply-templates select="items/li" />
		</ol>
		<xsl:if test="/response/result/site/admin=1">
			<sup>
				<a>
					<xsl:attribute name="href">/admin/addli/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
					+li
				</a>
				<a>
					<xsl:attribute name="href">/admin/upcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
					@up
				</a>
				<a>
					<xsl:attribute name="href">/admin/downcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
					@down
				</a>
				<a>
					<xsl:attribute name="href">/admin/deletecomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
					-delete
				</a>
			</sup>
		</xsl:if>
	</xsl:template>
	<xsl:template match="components">
		<xsl:apply-templates select="*" />
	</xsl:template>
	<xsl:template match="image">
		<xsl:choose>
			<xsl:when test="string-length(url)">
				<a>
					<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
					<img>
						<xsl:attribute name="src"><xsl:value-of select="src" /></xsl:attribute>
						<xsl:if test="name(../..) = 'aside' or name(../..) = 'site'">
							<xsl:attribute name="class">u-photo</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="alt"><xsl:value-of select="alt" /></xsl:attribute>
					</img>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<img>
					<xsl:attribute name="src"><xsl:value-of select="src" /></xsl:attribute>
					<xsl:if test="name(../..) = 'aside' or name(../..) = 'site'">
						<xsl:attribute name="class">u-photo</xsl:attribute>
					</xsl:if>
					<xsl:attribute name="alt"><xsl:value-of select="alt" /></xsl:attribute>
				</img>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="aside">
		<aside>
			<xsl:apply-templates select="images/image" />
			<xsl:choose>
				<xsl:when test="/response/result/site/admin=1">
					<form>
						<xsl:attribute name="action">/admin/updateaside/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						<xsl:attribute name="method">POST</xsl:attribute>
						<textarea>
							<xsl:attribute name="name">content</xsl:attribute>
							<xsl:value-of select="content" />
						</textarea>
						<input type="submit" value="Save" />
					</form>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test="string-length(content)">
						<h5>
							<xsl:value-of select="content" />
						</h5>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="/response/result/site/admin=1">
				<sup>
					<a>
						<xsl:attribute name="href">/admin/addasideimage/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						+image
					</a>
					<a>
						<xsl:attribute name="href">/admin/addasideauthor/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						+author
					</a>
					<a>
						<xsl:attribute name="href">/admin/upcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						@up
					</a>
					<a>
						<xsl:attribute name="href">/admin/downcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						@down
					</a>
					<a>
						<xsl:attribute name="href">/admin/deletecomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						-delete
					</a>
				</sup>
			</xsl:if>
			<xsl:for-each select="authors/author">
				<h6>
					<xsl:apply-templates select="." />
				</h6>
			</xsl:for-each>
		</aside>
	</xsl:template>	
	<xsl:template match="figure">
		<figure>
			<xsl:apply-templates select="images/image" />
			<xsl:choose>
				<xsl:when test="/response/result/site/admin=1">
					<form>
						<xsl:attribute name="action">/admin/updateaside/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						<xsl:attribute name="method">POST</xsl:attribute>
						<input>
							<xsl:attribute name="type">text</xsl:attribute>
							<xsl:attribute name="name">content</xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="content" /></xsl:attribute>
						</input>
						<input type="submit" value="Save" />
					</form>
				</xsl:when>
				<xsl:otherwise>
					<figcaption>
						<xsl:value-of select="content" />
					</figcaption>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="/response/result/site/admin=1">
				<sup>
					<a>
						<xsl:attribute name="href">/admin/addasideimage/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						+image
					</a>
					<a>
						<xsl:attribute name="href">/admin/upcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						@up
					</a>
					<a>
						<xsl:attribute name="href">/admin/downcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						@down
					</a>
					<a>
						<xsl:attribute name="href">/admin/deletecomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						-delete
					</a>
				</sup>
			</xsl:if>
		</figure>
	</xsl:template>
	<xsl:template match="nav">
		<nav>
			<h5>In this document...</h5>
			<ul>
				<xsl:for-each select="/response/result/site/posts/post/sections/section[components/heading]">
					<li>
						<a>
							<xsl:attribute name="href">#<xsl:value-of select="name" /></xsl:attribute>
							<xsl:value-of select="components/heading/content" />
						</a>
					</li>
				</xsl:for-each>
			</ul>
			<xsl:if test="/response/result/site/admin=1">
				<sup>
					<a>
						<xsl:attribute name="href">/admin/upcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						@up
					</a>
					<a>
						<xsl:attribute name="href">/admin/downcomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						@down
					</a>
					<a>
						<xsl:attribute name="href">/admin/deletecomponent/<xsl:value-of select="../../../../site_post_ID" />/<xsl:value-of select="section_component_ID" /></xsl:attribute>
						-delete
					</a>
				</sup>
			</xsl:if>
		</nav>
	</xsl:template>
	<xsl:template match="section">
		<section class="post">
			<xsl:attribute name="id"><xsl:value-of select="name" /></xsl:attribute>
			<xsl:apply-templates select="components" />
			<xsl:if test="/response/result/site/admin=1">
				<sup>
					<a>
						<xsl:attribute name="href">/admin/upsection/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="post_section_ID" /></xsl:attribute>
						@up
					</a>
					<a>
						<xsl:attribute name="href">/admin/downsection/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="post_section_ID" /></xsl:attribute>
						@down
					</a>
					<a>
						<xsl:attribute name="href">/admin/addheading/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						+heading
					</a>
					<a>
						<xsl:attribute name="href">/admin/addp/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						+p
					</a>
					<a>
						<xsl:attribute name="href">/admin/addul/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						+ul
					</a>
					<a>
						<xsl:attribute name="href">/admin/addol/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						+ol
					</a>
					<a>
						<xsl:attribute name="href">/admin/addaside/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						+aside
					</a>
					<a>
						<xsl:attribute name="href">/admin/addfigure/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						+figure
					</a>
					<a>
						<xsl:attribute name="href">/admin/addnav/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						+nav
					</a>
					<a>
						<xsl:attribute name="href">/admin/deletesection/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="post_section_ID" /></xsl:attribute>
						-delete
					</a>		
				</sup>
			</xsl:if>
		</section>
	</xsl:template>
	<xsl:template match="author">
		<xsl:choose>
			<xsl:when test="string-length(url)">
				<a>
					<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
					<xsl:attribute name="class">h-card <xsl:if test="name(../..)='post'"> p-author</xsl:if></xsl:attribute>
					<xsl:if test="string-length(rel) or name(../..)='post'">
						<xsl:attribute name="rel"><xsl:value-of select="rel" /><xsl:if test="name(../..)='post'"> author</xsl:if></xsl:attribute>
					</xsl:if>
					<xsl:value-of select="concat(firstname, ' ' , lastname)" />
				</a>
			</xsl:when>
			<xsl:otherwise>
				<span>
					<xsl:if test="string-length(class)">
						<xsl:attribute name="class"><xsl:value-of select="class" /></xsl:attribute>
					</xsl:if>
					<xsl:value-of select="concat(firstname, ' ' , lastname)" />
				</span>								
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="rating">
		<dt>
			Rating
		</dt>
		<dd class="rating">
			<img>
				<xsl:attribute name="src">
					<xsl:choose>
						<xsl:when test="value &gt; 0">
							<xsl:value-of select="on" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="off" />
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</img>
			<img>
				<xsl:attribute name="src">
					<xsl:choose>
						<xsl:when test="value &gt; 1">
							<xsl:value-of select="on" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="off" />
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</img>
			<img>
				<xsl:attribute name="src">
					<xsl:choose>
						<xsl:when test="value &gt; 2">
							<xsl:value-of select="on" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="off" />
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</img>
			<img>
				<xsl:attribute name="src">
					<xsl:choose>
						<xsl:when test="value &gt; 3">
							<xsl:value-of select="on" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="off" />
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</img>
			<img>
				<xsl:attribute name="src">
					<xsl:choose>
						<xsl:when test="value &gt; 4">
							<xsl:value-of select="on" />
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="off" />
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
			</img>
		</dd>
	</xsl:template>
	<xsl:template match="location" mode="spans">
		<span class="p-street-address"><xsl:value-of select="address1" /></span>
		<span class="p-extended-address"><xsl:value-of select="address2" /></span>
		<span class="p-post-office-box"><xsl:value-of select="pobox" /></span>
		<span class="p-locality"><xsl:value-of select="locality" /></span>
		<span class="p-region"><xsl:value-of select="region" /></span>
		<span class="p-postal-code"><xsl:value-of select="postalcode" /></span>
		<span class="p-country-name"><xsl:value-of select="country" /></span>
		<span class="p-latitude"><xsl:value-of select="latitude" /></span>
		<span class="p-longitude"><xsl:value-of select="longitude" /></span>
	</xsl:template>
	<xsl:template match="location" mode="inner">
		<xsl:choose>
			<xsl:when test="/response/result/site/admin=1">
				<form>
					<xsl:attribute name="action">/admin/updatelocationaddress/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
					<xsl:attribute name="method">POST</xsl:attribute>
					<input>
						<xsl:attribute name="type">text</xsl:attribute>
						<xsl:attribute name="name">address1</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="address1" /></xsl:attribute>
						<xsl:attribute name="placeholder">address1</xsl:attribute>
					</input>
					<input>
						<xsl:attribute name="type">text</xsl:attribute>
						<xsl:attribute name="name">address2</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="address2" /></xsl:attribute>
						<xsl:attribute name="placeholder">address2</xsl:attribute>
					</input>
					<input>
						<xsl:attribute name="type">text</xsl:attribute>
						<xsl:attribute name="name">pobox</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="pobox" /></xsl:attribute>
						<xsl:attribute name="placeholder">pobox</xsl:attribute>
					</input>
					<input>
						<xsl:attribute name="type">text</xsl:attribute>
						<xsl:attribute name="name">locality</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="locality" /></xsl:attribute>
						<xsl:attribute name="placeholder">locality</xsl:attribute>
					</input>
					<input>
						<xsl:attribute name="type">text</xsl:attribute>
						<xsl:attribute name="name">region</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="region" /></xsl:attribute>
						<xsl:attribute name="placeholder">region</xsl:attribute>
					</input>
					<input>
						<xsl:attribute name="type">text</xsl:attribute>
						<xsl:attribute name="name">postalcode</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="postalcode" /></xsl:attribute>
						<xsl:attribute name="placeholder">postalcode</xsl:attribute>
					</input>
					<input>
						<xsl:attribute name="type">text</xsl:attribute>
						<xsl:attribute name="name">country</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="country" /></xsl:attribute>
						<xsl:attribute name="placeholder">country</xsl:attribute>
					</input>
					<input>
						<xsl:attribute name="type">text</xsl:attribute>
						<xsl:attribute name="name">latitude</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="latitude" /></xsl:attribute>
						<xsl:attribute name="placeholder">latitude</xsl:attribute>
					</input>
					<input>
						<xsl:attribute name="type">text</xsl:attribute>
						<xsl:attribute name="name">longitude</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="longitude" /></xsl:attribute>
						<xsl:attribute name="placeholder">longitude</xsl:attribute>
					</input>
					<input type="submit" value="Save" />
				</form>
			</xsl:when>
			<xsl:when test="address1 or address2 or pobox or locality or region or postalcode or country">
				<dt>
					Address
				</dt>
				<dd>
					<address>
						<xsl:choose>
							<xsl:when test="string-length(url)">
								<a>
									<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
									<xsl:attribute name="class">p-location h-adr p-adr u-url</xsl:attribute>
									<xsl:apply-templates select="." mode="spans" />
								</a>
							</xsl:when>
							<xsl:otherwise>
								<span>
									<xsl:attribute name="class">p-location h-adr p-adr</xsl:attribute>
									<xsl:apply-templates select="." mode="spans" />
								</span>
							</xsl:otherwise>
						</xsl:choose>
					</address>
				</dd>
			</xsl:when>
			<xsl:when test="latitude or longitude">
				<dt>
					Position
				</dt>
				<dd>
					<address>
						<xsl:choose>
							<xsl:when test="string-length(url)">
								<a>
									<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
									<xsl:attribute name="class">p-location h-geo p-geo<xsl:if test="string-length(url) and string-length(website)&lt;1"> u-url</xsl:if></xsl:attribute>
									<span class="p-latitude"><xsl:value-of select="latitude" /></span>
									<span class="p-longitude"><xsl:value-of select="longitude" /></span>
								</a>
							</xsl:when>
							<xsl:otherwise>
								<span>
									<xsl:attribute name="class">p-location h-geo p-geo</xsl:attribute>
									<span class="p-latitude"><xsl:value-of select="latitude" /></span>
									<span class="p-longitude"><xsl:value-of select="longitude" /></span>
								</span>
							</xsl:otherwise>
						</xsl:choose>
					</address>
				</dd>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="location" mode="outer">
		<section class="h-card">
			<xsl:choose>
				<xsl:when test="/response/result/site/admin=1">
					<form>
						<xsl:attribute name="action">/admin/updatelocation/name/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						<xsl:attribute name="method">POST</xsl:attribute>
						<input>
							<xsl:attribute name="type">text</xsl:attribute>
							<xsl:attribute name="name">name</xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="name" /></xsl:attribute>
						</input>
						<input type="submit" value="Save" />
					</form>
				</xsl:when>
				<xsl:otherwise>
					<h4><xsl:value-of select="name" /></h4>
				</xsl:otherwise>
			</xsl:choose>
			<dl>
				<dt>
					<xsl:choose>
						<xsl:when test="/response/result/site/admin=1">
							Website
						</xsl:when>
						<xsl:otherwise>
							Name
						</xsl:otherwise>
					</xsl:choose>
				</dt>
				<dd class="p-name">
					<xsl:choose>
						<xsl:when test="/response/result/site/admin=1">
							<form>
								<xsl:attribute name="action">/admin/updatelocation/website/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
								<xsl:attribute name="method">POST</xsl:attribute>
								<input>
									<xsl:attribute name="type">text</xsl:attribute>
									<xsl:attribute name="name">website</xsl:attribute>
									<xsl:attribute name="value"><xsl:value-of select="website" /></xsl:attribute>
								</input>
								<input type="submit" value="Save" />
							</form>
						</xsl:when>
						<xsl:when test="string-length(website)">
							<a>
								<xsl:attribute name="href"><xsl:value-of select="website" /></xsl:attribute>
								<xsl:attribute name="class">u-url</xsl:attribute>
								<xsl:value-of select="name" />
							</a>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="name" />
						</xsl:otherwise>
					</xsl:choose>
				</dd>
				<xsl:apply-templates select="." mode="inner" />
				<xsl:apply-templates select="ratings/rating" />
			</dl>
			<hr />
		</section>
	</xsl:template>
	<xsl:template match="event">
		<section class="event">
			<xsl:choose>
				<xsl:when test="/response/result/site/admin=1">
					<form>
						<xsl:attribute name="action">/admin/updateevent/name/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						<xsl:attribute name="method">POST</xsl:attribute>
						<input>
							<xsl:attribute name="type">text</xsl:attribute>
							<xsl:attribute name="name">name</xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="name" /></xsl:attribute>
						</input>
						<input type="submit" value="Save" />
					</form>
				</xsl:when>
				<xsl:otherwise>
					<h4><xsl:value-of select="name" /></h4>
				</xsl:otherwise>
			</xsl:choose>
			<dl>
				<dt>
					Description
				</dt>
				<dd class="p-description">
					<xsl:choose>
						<xsl:when test="/response/result/site/admin=1">
							<form>
								<xsl:attribute name="action">/admin/updateevent/description/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
								<xsl:attribute name="method">POST</xsl:attribute>
								<input>
									<xsl:attribute name="type">text</xsl:attribute>
									<xsl:attribute name="name">description</xsl:attribute>
									<xsl:attribute name="value"><xsl:value-of select="description" /></xsl:attribute>
								</input>
								<input type="submit" value="Save" />
							</form>
						</xsl:when>
						<xsl:otherwise>							
							<xsl:value-of select="description" />
						</xsl:otherwise>
					</xsl:choose>
				</dd>
				<dt>
					Start Time
				</dt>
				<dd>
					<xsl:choose>
						<xsl:when test="/response/result/site/admin=1">
							<form>
								<xsl:attribute name="action">/admin/updateevent/start/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
								<xsl:attribute name="method">POST</xsl:attribute>
								<input>
									<xsl:attribute name="type">text</xsl:attribute>
									<xsl:attribute name="name">start</xsl:attribute>
									<xsl:attribute name="value"><xsl:value-of select="start" /></xsl:attribute>
								</input>
								<input type="submit" value="Save" />
							</form>
						</xsl:when>
						<xsl:otherwise>							
							<time>
								<xsl:attribute name="class">dt-start</xsl:attribute>
								<xsl:attribute name="datetime"><xsl:value-of select="start" /></xsl:attribute>
								<xsl:value-of select="start" />
							</time>
						</xsl:otherwise>
					</xsl:choose>
				</dd>
				<dt>
					End Time
				</dt>
				<dd>
					<xsl:choose>
						<xsl:when test="/response/result/site/admin=1">
							<form>
								<xsl:attribute name="action">/admin/updateevent/end/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
								<xsl:attribute name="method">POST</xsl:attribute>
								<input>
									<xsl:attribute name="type">text</xsl:attribute>
									<xsl:attribute name="name">end</xsl:attribute>
									<xsl:attribute name="value"><xsl:value-of select="end" /></xsl:attribute>
								</input>
								<input type="submit" value="Save" />
							</form>
						</xsl:when>
						<xsl:otherwise>							
							<time>
								<xsl:attribute name="class">dt-end</xsl:attribute>
								<xsl:attribute name="datetime"><xsl:value-of select="end" /></xsl:attribute>
								<xsl:value-of select="end" />
							</time>
						</xsl:otherwise>
					</xsl:choose>
				</dd>
				<dt>
					Duration
				</dt>
				<dd>
					<time>
						<xsl:attribute name="class">dt-duration</xsl:attribute>
						<xsl:attribute name="datetime"><xsl:value-of select="duration" /></xsl:attribute>
						<xsl:value-of select="duration" />
					</time>
				</dd>
				<xsl:apply-templates select="ratings/rating" />
			</dl>
			<hr />
		</section>
	</xsl:template>
	<xsl:template match="product">
		<section class="h-product">
		
			<xsl:choose>
				<xsl:when test="/response/result/site/admin=1">
					<form>
						<xsl:attribute name="action">/admin/updateproduct/name/<xsl:value-of select="../../site_post_ID" />/<xsl:value-of select="ID" /></xsl:attribute>
						<xsl:attribute name="method">POST</xsl:attribute>
						<input>
							<xsl:attribute name="type">text</xsl:attribute>
							<xsl:attribute name="name">name</xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="name" /></xsl:attribute>
						</input>
						<input type="submit" value="Save" />
					</form>
				</xsl:when>
				<xsl:otherwise>
					<h4 class="p-name"><xsl:value-of select="name" /></h4>
				</xsl:otherwise>
			</xsl:choose>
			<dl>
				<dt>
					Description
				</dt>
				<dd class="p-description">
					<xsl:value-of select="description" />
				</dd>
				<dt>
					Brand
				</dt>
				<dd class="p-brand">
					<xsl:value-of select="brand" />
				</dd>
				<dt>
					Model No.
				</dt>
				<dd class="u-identifier">
					<xsl:value-of select="identifier" />
				</dd>
				<dt>
					Price
				</dt>
				<dd class="p-price">
					<xsl:value-of select="price" />
				</dd>
				<xsl:apply-templates select="ratings/rating" />
			</dl>
			<hr />
		</section>
	</xsl:template>
	<xsl:template match="post">
		<main>
			<article>
				<xsl:attribute name="class">h-entry<xsl:if test="count(events/event)"> h-event</xsl:if></xsl:attribute>
				<xsl:choose>
					<xsl:when test="/response/result/site/admin=1 and count(../post) = 1">
						<form>
							<xsl:attribute name="action">/admin/renamepost/<xsl:value-of select="site_post_ID" /></xsl:attribute>
							<xsl:attribute name="method">POST</xsl:attribute>
							<input>
								<xsl:attribute name="type">text</xsl:attribute>
								<xsl:attribute name="name">name</xsl:attribute>
								<xsl:attribute name="value"><xsl:value-of select="name" /></xsl:attribute>
							</input>
							<input type="submit" value="Save" />
						</form>
					</xsl:when>
					<xsl:otherwise>
						<h3>
							<a>
								<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
								<xsl:attribute name="class">u-url p-name</xsl:attribute>
								<xsl:value-of select="name" />
							</a>
						</h3>
					</xsl:otherwise>
				</xsl:choose>
				<details>
					<summary>
						<span>
							Posted
						</span>
						<time>
							<xsl:attribute name="class">dt-published</xsl:attribute>
							<xsl:attribute name="datetime"><xsl:value-of select="published" /></xsl:attribute>
							<xsl:value-of select="published" />
						</time>
					</summary>
					<dl>
						<dt>
							Updated
						</dt>
						<dd>
							<time>
								<xsl:attribute name="class">dt-updated</xsl:attribute>
								<xsl:attribute name="datetime"><xsl:value-of select="updated" /></xsl:attribute>
								<xsl:value-of select="updated" />
							</time>
						</dd>
						<dt>
							Author
						</dt>
						<dd>
							<xsl:apply-templates select="authors/author" />
						</dd>
						<dt>
							Category
						</dt>
						<dd>
							<xsl:for-each select="categories/category">
								<xsl:choose>
									<xsl:when test="string-length(url)">
										<a>
											<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
											<xsl:attribute name="class">p-category</xsl:attribute>
											<xsl:value-of select="name" />
										</a>
									</xsl:when>
									<xsl:otherwise>
										<span>
											<xsl:attribute name="class">p-category</xsl:attribute>
											<xsl:value-of select="name" />
										</span>									
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</dd>
						<xsl:apply-templates select="locations/location[string-length(name)&lt;1]" mode="inner" />
						<dt>
							Entry #
						</dt>
						<dd>
							<a>
								<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
								<xsl:attribute name="class">u-uid</xsl:attribute>
								<xsl:value-of select="site_post_ID" />
							</a>
						</dd>
					</dl>
					<hr />
				</details>
				<xsl:apply-templates select="events/event" />
				<xsl:apply-templates select="locations/location[string-length(name)&gt;0]" mode="outer" />
				<xsl:apply-templates select="products/product" />
				<hr />
				<span class="e-content">
					<xsl:apply-templates select="sections/section" />
				</span>
				<xsl:if test="/response/result/site/admin=1 and count(../post) = 1">
					<sup>
						<a>
							<xsl:attribute name="href">/admin/addsection/<xsl:value-of select="site_post_ID" /></xsl:attribute>
							+section
						</a>
						<a>
							<xsl:attribute name="href">/admin/addevent/<xsl:value-of select="site_post_ID" /></xsl:attribute>
							+event
						</a>
						<a>
							<xsl:attribute name="href">/admin/addlocation/<xsl:value-of select="site_post_ID" /></xsl:attribute>
							+location
						</a>
						<a>
							<xsl:attribute name="href">/admin/addproduct/<xsl:value-of select="site_post_ID" /></xsl:attribute>
							+product
						</a>
						<a>
							<xsl:attribute name="href">/admin/deletepost/<xsl:value-of select="site_post_ID" /></xsl:attribute>
							-delete
						</a>
					</sup>
				</xsl:if>
				<hr />
			</article>
		</main>
	</xsl:template>
	<xsl:template match="/">
		<html>
			<head>
				<title><xsl:value-of select="response/result/site/name" /> - <xsl:choose><xsl:when test="count(response/result/site/posts/post)=1 and string-length(response/result/site/posts/post/name)"><xsl:value-of select="response/result/site/posts/post/name" /></xsl:when><xsl:otherwise><xsl:value-of select="response/result/site/heading" /></xsl:otherwise></xsl:choose></title>
				<meta charset="utf-8" />
				<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" />
				<link rel="stylesheet" type="text/css" href="/css/style.css" />
			</head>
			<body>
				<xsl:if test="count(response/result/site/posts/post)&gt;1">
					<xsl:attribute name="class">h-feed</xsl:attribute>
				</xsl:if>
				<input type="checkbox" id="checkbox_menu" />
				<xsl:apply-templates select="response/result/site" />
				<xsl:apply-templates select="response/result/site/posts/post" />
				<xsl:apply-templates select="response/result/site/navigation" />
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>