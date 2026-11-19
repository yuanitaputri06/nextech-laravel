-- ============================================================
-- NexTech: Tambah Kategori "Headphone" & Pindahkan Produk Sony
-- Jalankan di phpMyAdmin > Database: ecommerce > Tab SQL
-- ============================================================

-- LANGKAH 1: Tambah kategori "Headphone" (jika belum ada)
INSERT INTO categories (cat_title)
SELECT 'Headphone'
WHERE NOT EXISTS (
    SELECT 1 FROM categories WHERE LOWER(cat_title) LIKE '%headphone%'
);

-- LANGKAH 2: Pindahkan produk Sony dari kategori Aksesori ke Headphone
-- (hanya produk yang judulnya mengandung "sony" dan saat ini berkategori Aksesori)
UPDATE products
SET product_cat = (
    SELECT cat_id FROM categories
    WHERE LOWER(cat_title) LIKE '%headphone%'
    LIMIT 1
)
WHERE LOWER(product_title) LIKE '%sony%'
  AND product_cat = (
    SELECT cat_id FROM categories
    WHERE LOWER(cat_title) LIKE '%aksesori%'
       OR LOWER(cat_title) LIKE '%accessories%'
    LIMIT 1
  );

-- VERIFIKASI: Lihat hasil
SELECT p.product_id, p.product_title, c.cat_title
FROM products p
LEFT JOIN categories c ON p.product_cat = c.cat_id
WHERE LOWER(p.product_title) LIKE '%sony%';

-- Lihat semua kategori
SELECT * FROM categories ORDER BY cat_id;
