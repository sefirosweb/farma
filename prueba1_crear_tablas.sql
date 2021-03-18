DROP TABLE IF EXISTS blog;
CREATE TABLE blog (
	id INTEGER PRIMARY KEY,
	category TEXT NOT NULL,
	created_at TIMESTAMP NOT NULL,
	status INTEGER NOT NULL
);


DROP TABLE IF EXISTS blog_translations;
CREATE TABLE blog_translations (
	id INTEGER PRIMARY KEY,
	blog_id INTEGER NOT NULL,
	language TEXT NOT NULL,
	title TEXT NOT NULL,
	slug TEXT NOT NULL,
	created_at TIMESTAMP NOT NULL,
	FOREIGN KEY (blog_id) REFERENCES blog (id) 
);

INSERT INTO blog VALUES ( 1, 'NEWS', CURRENT_TIMESTAMP, 1 );

INSERT INTO blog_translations VALUES ( 1, 1, 'es', 'Noticias del mundo', 'noticias_del_mundo', CURRENT_TIMESTAMP);
INSERT INTO blog_translations VALUES ( 2, 1, 'en', 'News of the world', 'news_of_the_world', CURRENT_TIMESTAMP);
INSERT INTO blog_translations VALUES ( 3, 1, 'cat', 'Noticies del mon', 'noticies_del_mon', CURRENT_TIMESTAMP);

SELECT * FROM blog;
SELECT * FROM blog_translations;

