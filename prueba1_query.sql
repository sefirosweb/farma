
SELECT
    blog.id,
    blog.category,
    -- title_es --
    (
        SELECT
            blog_translations.title
        FROM
            blog_translations
        WHERE
            blog_translations.blog_id = blog.id
            AND blog_translations.language = 'es'
            LIMIT 1
    ) AS title_es,
    -- title_en --
    (
        SELECT
            blog_translations.title
        FROM
            blog_translations
        WHERE
            blog_translations.blog_id = blog.id
            AND blog_translations.language = 'en'
            LIMIT 1
    ) AS title_en,
    -- title_cat --
    (
        SELECT
            blog_translations.title
        FROM
            blog_translations
        WHERE
            blog_translations.blog_id = blog.id
            AND blog_translations.language = 'cat'
            LIMIT 1
    ) AS title_cat
    
FROM
    blog
WHERE DATE(blog.created_at) = '2021-03-18';
