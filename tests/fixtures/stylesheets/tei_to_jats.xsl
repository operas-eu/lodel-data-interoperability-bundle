<?xml version="1.0" encoding="UTF-8"?>

<!--
This file is part of the CRAFT-OA Project (https://www.craft-oa.eu/)
funded by the European Union (HORIZON-INFRA-2022-EOSC-01 Grant Agreement: 101094397).

Developments have been made at OpenEdition Center, a french CNRS Support and Research Unit (UAR 2504)
associated with Aix-Marseille University, the EHESS and Avignon University.

Authors: João Martins, Jean-Christophe Souplet, Nicolas Vernot Cortes.

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
-->

<xsl:stylesheet version="2.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:tei="http://www.tei-c.org/ns/1.0"
	xmlns="http://www.ncbi.nlm.nih.gov/JATS"
	exclude-result-prefixes="tei">

	<!-- Racine TEI -->
	<xsl:template match="*:TEI">
		<article>
			<front>
				<article-meta>
					<title-group>
						<article-title>
							<xsl:value-of select="*:teiHeader/*:fileDesc/*:titleStmt/*:title"/>
						</article-title>
					</title-group>
					<contrib-group>
						<contrib contrib-type="author">
							<name>
								<surname>
									<xsl:value-of select="*:teiHeader/*:fileDesc/*:titleStmt/*:author/*:persName/*:surname"/>
								</surname>
								<given-names>
									<xsl:value-of select="*:teiHeader/*:fileDesc/*:titleStmt/*:author/*:persName/*:forename"/>
								</given-names>
							</name>
						</contrib>
					</contrib-group>
				</article-meta>
			</front>
			<body>
				<xsl:apply-templates select="*:text/*:body/*:div"/>
			</body>
		</article>
	</xsl:template>

	<!-- Conversion des divs TEI en sections JATS -->
	<xsl:template match="*:div">
		<sec>
			<xsl:apply-templates/>
		</sec>
	</xsl:template>

	<!-- head devient title -->
	<xsl:template match="*:head">
		<title>
			<xsl:apply-templates/>
		</title>
	</xsl:template>

	<!-- p reste p -->
	<xsl:template match="*:p">
		<p>
			<xsl:apply-templates/>
		</p>
	</xsl:template>

</xsl:stylesheet>
