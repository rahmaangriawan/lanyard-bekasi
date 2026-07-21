<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:sm="http://www.sitemaps.org/schemas/sitemap/0.9">
  <xsl:output method="html" encoding="UTF-8" indent="yes"/>
  <xsl:template match="/">
    <html lang="id">
      <head>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <title>Sitemap | Pusat Tali Lanyard</title>
        <style>
          body { margin: 0; background: #f8fafc; color: #0f172a; font-family: Arial, sans-serif; }
          main { max-width: 960px; margin: 0 auto; padding: 48px 24px; }
          h1 { margin: 0; font-size: 28px; } p { color: #475569; line-height: 1.6; }
          table { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
          th, td { padding: 14px 16px; border-bottom: 1px solid #e2e8f0; text-align: left; font-size: 14px; }
          th { background: #0f172a; color: #fff; font-size: 12px; text-transform: uppercase; letter-spacing: .05em; }
          tr:last-child td { border-bottom: 0; } a { color: #0369a1; font-weight: 600; word-break: break-word; } a:hover { color: #0c4a6e; }
        </style>
      </head>
      <body><main>
        <xsl:choose>
          <xsl:when test="sm:sitemapindex">
            <h1>Indeks Sitemap</h1><p>Pilih kategori untuk melihat URL yang tercantum di dalamnya.</p>
            <table><thead><tr><th>Kategori sitemap</th></tr></thead><tbody>
              <xsl:for-each select="sm:sitemapindex/sm:sitemap"><tr><td><a href="{sm:loc}"><xsl:value-of select="sm:loc"/></a></td></tr></xsl:for-each>
            </tbody></table>
          </xsl:when>
          <xsl:otherwise>
            <h1>Daftar URL Sitemap</h1><p>URL publik yang dapat diindeks untuk Pusat Tali Lanyard.</p>
            <table><thead><tr><th>URL</th><th>Terakhir diperbarui</th></tr></thead><tbody>
              <xsl:for-each select="sm:urlset/sm:url"><tr><td><a href="{sm:loc}"><xsl:value-of select="sm:loc"/></a></td><td><xsl:value-of select="sm:lastmod"/></td></tr></xsl:for-each>
            </tbody></table>
          </xsl:otherwise>
        </xsl:choose>
      </main></body>
    </html>
  </xsl:template>
</xsl:stylesheet>
