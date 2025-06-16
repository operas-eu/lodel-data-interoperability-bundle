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
    xmlns:j="http://www.ncbi.nlm.nih.gov/JATS"
    xmlns="http://www.tei-c.org/ns/1.0"
    exclude-result-prefixes="j">

    <!--
        This XSLT transforms a JATS XML document into a basic TEI XML structure.
        It extracts the article title and author(s) from the JATS front matter and 
        creates a minimal <teiHeader>. It also transforms sections and paragraphs.
    -->

    <!-- Match the root and build the TEI structure -->
    <xsl:template match="/">
        <TEI>
            <teiHeader>
                <fileDesc>
                    <titleStmt>
                        <!-- Extract the article title from JATS -->
                        <title>
                            <xsl:value-of select="//*:article-title"/>
                        </title>
                        <!-- Extract the first author (given names and surname) -->
                        <author>
                            <persName>
                                <surname>
                                    <xsl:value-of select="//*:contrib[@contrib-type='author']/*:name/*:surname"/>
                                </surname>
                                <forename>
                                    <xsl:value-of select="//*:contrib[@contrib-type='author']/*:name/*:given-names"/>
                                </forename>
                            </persName>
                        </author>
                    </titleStmt>
                    <!-- Static publication statement -->
                    <publicationStmt>
                        <p>Unpublished conversion from JATS to TEI</p>
                    </publicationStmt>
                    <!-- Source description stating JATS origin -->
                    <sourceDesc>
                        <p>Original source in JATS format</p>
                    </sourceDesc>
                </fileDesc>
            </teiHeader>

            <!-- Apply templates to body content -->
            <text>
                <xsl:apply-templates select="//*:body"/>
            </text>
        </TEI>
    </xsl:template>

    <!-- Transform <body> to TEI <body> -->
    <xsl:template match="*:body">
        <body>
            <xsl:apply-templates/>
        </body>
    </xsl:template>

    <!-- Transform each <sec> into a <div> -->
    <xsl:template match="*:sec">
        <div>
            <xsl:apply-templates/>
        </div>
    </xsl:template>

    <!-- Transform JATS <title> into TEI <head> -->
    <xsl:template match="*:title">
        <head><xsl:value-of select="."/></head>
    </xsl:template>

    <!-- Transform JATS <p> into TEI <p> -->
    <xsl:template match="*:p">
        <p><xsl:value-of select="."/></p>
    </xsl:template>

</xsl:stylesheet>
