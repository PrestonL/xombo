<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="no"/>
		<xsl:template match="/">
			<html>
				<head>
					<meta charset="utf-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1" />
					<title>XML-RPC response</title>
					<link href="/favicon.ico" rel="shortcut icon" />
				</head>
				<body>
					<div data-role="page">
						<div data-role="header" data-theme="a" id="classHeader" data-position="fixed">
							<a href="../" data-icon="arrow-u" data-iconpos="notext" data-direction="reverse" class="ui-btn-right jqm-home ui-btn ui-btn-up-b ui-btn-icon-notext ui-btn-corner-all ui-shadow" title="Back" data-theme="a"><span class="ui-btn-inner ui-btn-corner-all"><span class="ui-btn-text">Back</span><span class="ui-icon ui-icon-home ui-icon-shadow"></span></span></a>
						</div>
						<div data-role="content">
							<xsl:choose>
								<xsl:when test="count(responses)>0">
									<xsl:apply-templates select="responses" />
								</xsl:when>
								<xsl:when test="response">
									<xsl:apply-templates select="response" />
								</xsl:when>
							</xsl:choose>
						</div>
					</div>
				</body>
			</html>
		</xsl:template>
		<xsl:template match="responses">
			<xsl:apply-templates select="response" />
		</xsl:template>
		<xsl:template match="response">
			<xsl:choose>
				<xsl:when test="result">
					<xsl:apply-templates select="result" />
				</xsl:when>
				<xsl:when test="error">
					<ul data-role="listview" data-inset="true">
						<xsl:apply-templates select="error" />
					</ul>
				</xsl:when>
			</xsl:choose>
			<h6>
				response #<xsl:value-of select="id" /> via <a href="/">XOMBO XML-RPC <xsl:value-of select="xmlrpc" /></a>
			</h6>
		</xsl:template>
		<xsl:template match="error">
			<li>
				<p><i>Error <xsl:value-of select="id" /></i></p>
				<p><strong><xsl:value-of select="code" /></strong> <xsl:value-of select="message" /></p>
			</li>
		</xsl:template>
		<xsl:template match="result">
			<xsl:choose>
				<xsl:when test="count(*)=1 and *[1]/ID">
					<xsl:for-each select="*">
						<form>
							<xsl:attribute name="method">post</xsl:attribute>
							<xsl:attribute name="action">.</xsl:attribute>
							<ul data-role="listview" data-inset="true">
								<xsl:for-each select="*">
									<li>
										<label>
											<strong>
												<xsl:value-of select="name(.)" />
											</strong>
										</label>
										<input>
											<xsl:choose>
												<xsl:when test="name(.)='StartTime' or name(.)='EndTime'">
													<xsl:attribute name="type">date</xsl:attribute>
												</xsl:when>
												<xsl:otherwise>
													<xsl:attribute name="type">text</xsl:attribute>
												</xsl:otherwise>
											</xsl:choose>
											<xsl:attribute name="value"><xsl:value-of select="normalize-space(.)" /></xsl:attribute>
											<xsl:attribute name="name"><xsl:value-of select="name(.)" /></xsl:attribute>
											<xsl:attribute name="id"><xsl:value-of select="name(.)" /></xsl:attribute>
										</input>
									</li>
								</xsl:for-each>
							</ul>
							<fieldset class="ui-grid-a">
								<div class="ui-block-a">
									<a>
										<xsl:attribute name="href">../delete/<xsl:value-of select="normalize-space(ID)" />/</xsl:attribute>
										<xsl:attribute name="data-rel">dialog</xsl:attribute>
										<xsl:attribute name="data-transition">pop</xsl:attribute>
										<xsl:attribute name="data-role">button</xsl:attribute>
										<xsl:attribute name="data-icon">delete</xsl:attribute>
										<xsl:attribute name="data-theme">a</xsl:attribute>
										Delete
									</a>
								</div>
								<div class="ui-block-b">
									<button type="submit" data-theme="b" data-role="button" data-icon="check">Save</button>
								</div>
							</fieldset>
						</form>
					</xsl:for-each>
				</xsl:when>
				<xsl:when test="*[1]/ID">
					<ul data-role="listview" data-inset="true">
						<xsl:for-each select="*">
							<li>
								<a>
									<xsl:attribute name="href">../view/<xsl:value-of select="normalize-space(ID)" />/</xsl:attribute>
									<xsl:value-of select="normalize-space(ID)" />
								</a>
							</li>
						</xsl:for-each>
					</ul>
				</xsl:when>
				<xsl:when test=".=1">
					<a href="../../describe/" data-role="button" data-icon="delete">OK</a>
				</xsl:when>
				<xsl:when test=".=0">
					<a href="../../describe/" data-role="button" data-icon="delete">Failed</a>
				</xsl:when>
				<xsl:when test="count(*)">
					<ul data-role="listview" data-inset="true">
						<xsl:for-each select="*">
							<li>
								<a>
									<xsl:attribute name="href">./<xsl:value-of select="name(.)" />/</xsl:attribute>
									<xsl:value-of select="name(.)" /> (<xsl:value-of select="normalize-space(.)" />)
								</a>
							</li>
						</xsl:for-each>
					</ul>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="." />
				</xsl:otherwise>
			</xsl:choose>
		</xsl:template>
</xsl:stylesheet>