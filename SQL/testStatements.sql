# SELECT c.id,c.text, COUNT(q.category_id) AS number FROM question q LEFT JOIN category c ON q.category_id = c.id GROUP BY q.category_id ;

SELECT * FROM question WHERE category_id IN (1, 2)