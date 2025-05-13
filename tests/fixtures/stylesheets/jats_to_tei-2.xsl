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
    xmlns="http://www.tei-c.org/ns/1.0"
    exclude-result-prefixes="#all">

    <!-- 
        XSLT step 2:
        This stylesheet adds sequential xml:id attributes to each <p> element in the TEI document.
        It assumes the document is already in TEI format, as produced by step1.
    -->

    <!-- 
        Elements and attributes are copied as-is unless explicitly overridden.
    -->
    <xsl:template match="@*|node()">
		<xsl:copy>
			<xsl:apply-templates select="@*|node()" />
		</xsl:copy>
	</xsl:template>

    <!-- 
        Match each <p> element in the TEI namespace.
        Adds an xml:id like "p1", "p2", "p3", etc., based on the position within its parent.
    -->
    <xsl:template match="p">
        <p xml:id="{concat('p', position())}">
            <xsl:apply-templates select="@* | node()"/>
        </p>
    </xsl:template>

</xsl:stylesheet>
