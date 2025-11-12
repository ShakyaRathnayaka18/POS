# How to Add Colorful Category Icons Using Freepik MCP

This guide explains how to search for and download colorful PNG icons from Freepik using MCP (Model Context Protocol) tools.

## Overview

The Freepik MCP server provides tools to search for icons and download them directly. This is perfect for adding professional, colorful icons to your application without manually browsing websites.

## Step 1: Search for Icons

Use the `mcp__mcp-freepik__search_icons` tool to find icons:

```javascript
mcp__mcp-freepik__search_icons({
  term: "beverage drink colorful",
  per_page: 5
})
```

**Parameters:**
- `term`: Search keywords (e.g., "bakery bread colorful", "grocery shopping cart")
- `per_page`: Number of results to return (default: 3, max varies)
- `order`: Sort order - "relevance" (default) or "recent"

**Pro Tips for Better Results:**
- Include "colorful" in your search term to get vibrant icons
- Be specific: "bakery croissant colorful" is better than just "bakery"
- Try different variations: "beverage drink bottle" vs "soda soft drink"

**Example Response:**
```json
{
  "data": [
    {
      "id": 6966280,
      "name": "Soda",
      "thumbnails": [{
        "width": 128,
        "height": 128,
        "url": "https://cdn-icons-png.freepik.com/128/6966/6966280.png"
      }],
      "free_svg": false
    }
  ]
}
```

Note: `free_svg: false` means SVG download requires premium, but PNG is usually available.

## Step 2: Download Icons as PNG

Once you find an icon you like, download it using `curl`:

```bash
curl -o "public/images/category-icons/beverages.png" "https://cdn-icons-png.freepik.com/128/6966/6966280.png"
```

**URL Pattern:**
- The PNG URL follows this pattern: `https://cdn-icons-png.freepik.com/128/{id}/{id}.png`
- Size options: `/128/` (128px), `/256/` (256px), `/512/` (512px)

**Why PNG instead of SVG?**
- PNG downloads are free and don't require premium Freepik subscription
- SVG downloads require `mcp__mcp-freepik__download_icon_by_id` which returns 403 Forbidden for premium icons
- PNG quality at 128px is perfect for category icons

## Step 3: Update Database

After downloading icons, update your database to use them:

```sql
-- Using MySQL MCP
UPDATE categories
SET icon = 'beverages.png',
    updated_at = NOW()
WHERE cat_name = 'Beverages'
```

Or add new categories:

```sql
INSERT INTO categories (cat_name, description, icon, created_at, updated_at)
VALUES ('Stationery', 'Pens, Notebooks, Papers, and Office Supplies', 'stationery.png', NOW(), NOW())
```

## Complete Workflow Example

### 1. Search for a colorful bakery icon
```javascript
mcp__mcp-freepik__search_icons({
  term: "bakery croissant colorful",
  per_page: 5
})
```

### 2. Review results and pick the best one
Look for icons with colorful gradients or multiple colors. Check the `thumbnails[0].url` to preview.

### 3. Download the icon
```bash
curl -o "public/images/category-icons/bakery.png" "https://cdn-icons-png.freepik.com/128/4608/4608979.png"
```

### 4. Update the database
```sql
UPDATE categories
SET icon = 'bakery.png'
WHERE cat_name = 'Bakery'
```

## Finding Different Icon Styles

To find icons with specific styles:

**Colorful/Flat Icons:**
- Add "flat colorful" or "gradient" to search terms
- Example: "pet dog flat colorful"

**Specific Colors:**
- Include color in search: "blue bottle", "red meat", "green vegetables"

**Icon Types:**
- "outline" for line-based icons
- "fill" or "solid" for filled icons
- "gradient" for modern colorful gradients
- "3d" for three-dimensional icons

## Troubleshooting

**Problem:** All search results show `free_svg: false`
- **Solution:** Use PNG URLs instead of the download API. PNGs are freely accessible.

**Problem:** 403 Forbidden when downloading
- **Solution:** Don't use `mcp__mcp-freepik__download_icon_by_id` for premium icons. Use direct PNG URLs with `curl`.

**Problem:** Icons are black and white
- **Solution:** Add "colorful", "gradient", or "flat" to your search term. Try different search variations.

**Problem:** Icon doesn't match category
- **Solution:** Try more specific terms: "grocery shopping cart" instead of just "grocery"

## Icon Naming Convention

When saving icons, use descriptive, lowercase, hyphenated names:

-  `beverages.png`
-  `pet-supplies.png`
-  `personal-care.png`
- L `icon1.png`
- L `BeveragesIcon.png`

## Best Practices

1. **Always preview icons** before downloading by checking the thumbnail URL
2. **Keep icons consistent** - all colorful or all monochrome, not mixed
3. **Use descriptive search terms** - be specific about what you want
4. **Save with meaningful names** - use category names, not generic names
5. **Test in dark mode** - ensure icons look good on both light and dark backgrounds
6. **Maintain aspect ratio** - use `object-contain` in CSS to preserve icon proportions

## Resources

- Freepik Icon Library: https://www.freepik.com/icons
- MCP Freepik Documentation: Check your MCP server configuration
- Icon Preview: Use thumbnail URLs to preview before downloading
