<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="no"/>
		<xsl:template match="/">
			<html lang="en">
				<head>
					<meta charset="utf-8" />
					<meta name="viewport" content="width=device-width, initial-scale=1" />
					<title>XOMBO Todo List</title>
					<link href="/favicon.ico" rel="shortcut icon" />
					<link rel="stylesheet" type="text/css" href="/css/todo.css" />
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
					<script type="text/javascript" src="/js/jquery.min.js" />
					<script type="text/javascript" src="/js/jquery.xslt.js" />
					<script type="text/javascript" src="/js/site.js" />
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
					<ul>
						<xsl:apply-templates select="error" />
					</ul>
				</xsl:when>
			</xsl:choose>
		</xsl:template>
		<xsl:template match="error">
			<li>
				<p><i>Error <xsl:value-of select="id" /></i></p>
				<p><strong><xsl:value-of select="code" /></strong> <xsl:value-of select="message" /></p>
			</li>
		</xsl:template>
		<xsl:template match="result">
			<form>
				<xsl:attribute name="method">POST</xsl:attribute>
				<xsl:attribute name="action">/todo/saveall</xsl:attribute>
				<h1>XOMBO Demo</h1>
				<h2>Todo List</h2>
				<ul>
					<xsl:apply-templates select="*" />
					<li>
						<input type="text" name="todo_new" placeholder="New Item" />
						<input type="submit" value="Save" />
					</li>
				</ul>
			</form>
		</xsl:template>
		<xsl:template match="todo">
			<li>
				<xsl:attribute name="id">li_todo<xsl:value-of select="ID" /></xsl:attribute>
				<input>
					<xsl:attribute name="type">radio</xsl:attribute>
					<xsl:attribute name="name">todo_selected</xsl:attribute>
					<xsl:attribute name="id">todo_selected<xsl:value-of select="ID" /></xsl:attribute>
				</input>
				<label>
					<xsl:attribute name="for">todo_selected<xsl:value-of select="ID" /></xsl:attribute>
					<input>
						<xsl:attribute name="type">text</xsl:attribute>
						<xsl:attribute name="name">todo_text[<xsl:value-of select="ID" />]</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="title" /></xsl:attribute>
					</input>
				</label>
				<a>
					<xsl:choose>
						<xsl:when test="completed=1">
							<xsl:attribute name="class">complete</xsl:attribute>
							<xsl:attribute name="href">/todo/complete/<xsl:value-of select="ID" />/0</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="class">incomplete</xsl:attribute>
							<xsl:attribute name="href">/todo/complete/<xsl:value-of select="ID" />/1</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:attribute name="data-target">#li_todo<xsl:value-of select="ID" /></xsl:attribute>
					<xsl:attribute name="data-template">/todo/xsl</xsl:attribute>
				</a>
			</li>
		</xsl:template>
		</xsl:stylesheet>