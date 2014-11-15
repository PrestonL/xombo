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
		</ul>
	</xsl:template>
	<xsl:template match="navigation">
		<xsl:if test="next | previous">
			<nav>
				<xsl:if test="string-length(previous)">
					<a>
						<xsl:attribute name="href"><xsl:value-of select="previous" /></xsl:attribute>
						Newer
					</a>
				</xsl:if>
				<xsl:if test="string-length(next)">
					<a>
						<xsl:attribute name="href"><xsl:value-of select="next" /></xsl:attribute>
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
		<header>		
			<h2>
				<xsl:apply-templates select="images/image" />
			</h2>
			<hgroup>
				<h1>
					<xsl:attribute name="class">p-name</xsl:attribute>
					<a>
						<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
						<xsl:attribute name="class">u-url</xsl:attribute>
						<xsl:attribute name="rel">me</xsl:attribute>
						<xsl:value-of select="name" />
					</a>
				</h1>
				<h2>
					<xsl:value-of select="heading" />
				</h2>
			</hgroup>
			<xsl:apply-templates select="navigation/menus" />
			<hr />
		</header>
	</xsl:template>
	<xsl:template match="heading">
		<h4><xsl:value-of select="content" /></h4>
	</xsl:template>
	<xsl:template match="p">
		<p>
			<xsl:if test="summary and summary != 'false' and summary != 0">
				<xsl:attribute name="class">p-summary</xsl:attribute>
			</xsl:if>
			<xsl:value-of select="content" />
			<xsl:if test="summary and summary != 'false' and summary != 0">
				<a>
					<xsl:attribute name="class">readmore</xsl:attribute>
					<xsl:attribute name="href"><xsl:value-of select="../../../../url" /></xsl:attribute>
					read more...
				</a>
			</xsl:if>
		</p>
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
						<xsl:if test="name(..) = 'aside' or name(..) = 'site'">
							<xsl:attribute name="class">u-photo</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="alt"><xsl:value-of select="alt" /></xsl:attribute>
					</img>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<img>
					<xsl:attribute name="src"><xsl:value-of select="src" /></xsl:attribute>
					<xsl:if test="name(..) = 'aside'">
						<xsl:attribute name="class">u-photo</xsl:attribute>
					</xsl:if>
					<xsl:attribute name="alt"><xsl:value-of select="alt" /></xsl:attribute>
				</img>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="aside">
		<aside>
			<xsl:apply-templates select="image" />
			<xsl:if test="string-length(caption)">
				<h5>
					<xsl:value-of select="caption" />
				</h5>
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
			<xsl:apply-templates select="image" />
			<figcaption>
				This is another piece of text that accompanies a different image in the figure tag.
			</figcaption>
		</figure>
	</xsl:template>
	<xsl:template match="nav">
		<nav>
			<h5>In this document...</h5>
			<ul>
				<xsl:for-each select="/response/result/site/posts/post/sections/section">
					<li>
						<a>
							<xsl:attribute name="href">#<xsl:value-of select="name" /></xsl:attribute>
							<xsl:value-of select="heading" />
						</a>
					</li>				
				</xsl:for-each>
			</ul>
		</nav>
	</xsl:template>
	<xsl:template match="section">
		<section class="post">
			<xsl:attribute name="id"><xsl:value-of select="name" /></xsl:attribute>
			<xsl:apply-templates select="components" />
		</section>
	</xsl:template>
	<xsl:template match="author">
		<xsl:choose>
			<xsl:when test="string-length(url)">
				<a>
					<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
					<xsl:if test="string-length(class)">
						<xsl:attribute name="class"><xsl:value-of select="class" /></xsl:attribute>
					</xsl:if>
					<xsl:if test="string-length(rel)">
						<xsl:attribute name="rel"><xsl:value-of select="rel" /></xsl:attribute>
					</xsl:if>
					<xsl:value-of select="concat(firstname, ' ' , lastname)" />
				</a>
			</xsl:when>
			<xsl:otherwise>
				<span>
					<xsl:if test="string-length(class)">
						<xsl:attribute name="class"><xsl:value-of select="class" /></xsl:attribute>
					</xsl:if>
					<xsl:value-of select="name" />
				</span>								
			</xsl:otherwise>
		</xsl:choose>
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
			<h4>Location</h4>
			<dl>
				<dt>
					Name
				</dt>
				<dd class="p-name">
					<xsl:choose>
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
			</dl>
			<hr />
		</section>
	</xsl:template>
	<xsl:template match="event">
		<section class="event">
			<h4>Event</h4>
			<dl>
				<dt>
					Description
				</dt>
				<dd class="p-description">
					<xsl:value-of select="description" />
				</dd>
				<dt>
					Start Time
				</dt>
				<dd>
					<time>
						<xsl:attribute name="class">dt-start</xsl:attribute>
						<xsl:attribute name="datetime"><xsl:value-of select="start" /></xsl:attribute>
						<xsl:value-of select="start" />
					</time>
				</dd>
				<dt>
					End Time
				</dt>
				<dd>
					<time>
						<xsl:attribute name="class">dt-end</xsl:attribute>
						<xsl:attribute name="datetime"><xsl:value-of select="end" /></xsl:attribute>
						<xsl:value-of select="end" />
					</time>
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
			</dl>
			<hr />
		</section>
	</xsl:template>
	<xsl:template match="post">
		<main>
			<article>
				<xsl:attribute name="class">h-entry e-content<xsl:if test="count(events/event)"> h-event</xsl:if></xsl:attribute>
				<h3>
					<a>
						<xsl:attribute name="href"><xsl:value-of select="url" /></xsl:attribute>
						<xsl:attribute name="class">u-url p-name</xsl:attribute>
						<xsl:value-of select="name" />
					</a>
				</h3>
				<details>
					<summary>
						<label>
							Posted
						</label>
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
								<xsl:value-of select="ID" />
							</a>
						</dd>
					</dl>
					<hr />
				</details>
				<xsl:apply-templates select="events/event" />
				<xsl:apply-templates select="locations/location[string-length(name)&gt;0]" mode="outer" />
				<hr />
				<xsl:apply-templates select="sections/section" />
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