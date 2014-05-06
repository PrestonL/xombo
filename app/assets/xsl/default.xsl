<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="no"/>
		<xsl:template match="/">
			<html lang="en">
				<head>
					<meta charset="utf-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1" />
					<title>XOMBO Cloud</title>
					<link href="/favicon.ico" rel="shortcut icon" />
				</head>
				<body>
					<div>
						<div>
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
			<xsl:apply-templates select="*" />
		</xsl:template>
		<xsl:template match="error">
			<ul>			
				<li>
					<p><i>Error <xsl:value-of select="id" /></i></p>
					<p><strong><xsl:value-of select="code" /></strong> <xsl:value-of select="message" /></p>
				</li>
			</ul>
		</xsl:template>
		<xsl:template match="result">
			<xsl:choose>
				<xsl:when test="count(*)=1 and *[1]/ID">
					<xsl:for-each select="*">
						<form>
							<xsl:attribute name="method">post</xsl:attribute>
							<xsl:attribute name="action">.</xsl:attribute>
							<ul>
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
										<xsl:attribute name="href">../../delete/<xsl:value-of select="normalize-space(ID)" />/</xsl:attribute>
										Delete
									</a>
								</div>
								<div class="ui-block-b">
									<input type="submit" value="Save" />
								</div>
							</fieldset>
						</form>
					</xsl:for-each>
				</xsl:when>
				<xsl:when test="*[1]/ID">
					<ul>
						<xsl:for-each select="*">
							<li>
								<a>
									<xsl:attribute name="href">../view/<xsl:value-of select="normalize-space(ID)" />/</xsl:attribute>
									<xsl:value-of select="normalize-space(name)" />
								</a>
							</li>
						</xsl:for-each>
					</ul>
				</xsl:when>
				<xsl:when test=".=1">
					<a href="../../describe/">OK</a>
				</xsl:when>
				<xsl:when test=".=0">
					<a href="../../describe/">Failed</a>
				</xsl:when>
				<xsl:when test="count(*)">
					<ul>
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
