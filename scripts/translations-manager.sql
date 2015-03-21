DROP FUNCTION IF EXISTS CAPITAL;
CREATE FUNCTION CAPITAL(input VARCHAR(1024))
    RETURNS VARCHAR(1024)
DETERMINISTIC
    BEGIN
        DECLARE len INT;
        DECLARE i INT;

        SET len = CHAR_LENGTH(input);
        SET input = LOWER(input);
        SET i = 0;
        SET input = CONCAT(LEFT(input, i), UPPER(MID(input, i + 1, 1)), right(input, len - i - 1));
        SET i = i + 1;
        WHILE (i < len) DO
            SET i = LOCATE(' ', input, i);
            IF i = 0 OR i = len
            THEN
                SET i = len;
            ELSE
                SET input = CONCAT(LEFT(input, i), UPPER(MID(input, i + 1, 1)), right(input, len - i - 1));
                SET i = i + 1;
            END IF;
        END WHILE;

        RETURN input;
    END;

CREATE OR REPLACE VIEW en_translations AS
    SELECT *
    FROM ltm_translations
    WHERE locale = 'en';

CREATE OR REPLACE VIEW ru_translations AS
    SELECT *
    FROM ltm_translations
    WHERE locale = 'ru';


CREATE OR REPLACE VIEW missing_translations AS
    SELECT
        ru.id,
        ru.group,
        ru.key,
        ru.value ru_value,
        en.value en_value
    FROM ru_translations ru
        JOIN en_translations en
            ON ru.`group` = en.`group` AND ru.`key` = en.`key`
    WHERE 1 = 1
          AND ru.value is null or ru.value = en.value
          AND concat(en.`group`, '.', en.`key`) NOT IN ('messages.lang_en', 'messages.lang_ru', 'messages.use-site');

SELECT *
FROM missing_translations;


CREATE OR REPLACE VIEW have_translations AS
    SELECT
        ru.id,
        ru.group,
        ru.key,
        ru.value ru_value,
        en.value en_value
    FROM ru_translations ru
        JOIN en_translations en
            ON ru.`group` = en.`group` AND ru.`key` = en.`key`
    WHERE 1 = 1
          AND ru.value is not null and ru.value  <> en.value
          AND concat(en.`group`, '.', en.`key`) NOT IN ('messages.lang_en', 'messages.lang_ru', 'messages.use-site');

SELECT *
FROM have_translations;

DROP TEMPORARY TABLE IF EXISTS ru_missing_translations;
CREATE TEMPORARY TABLE ru_missing_translations
    AS SELECT *
       FROM missing_translations;

DROP TEMPORARY TABLE IF EXISTS ru_have_translations;
CREATE TEMPORARY TABLE ru_have_translations
    AS SELECT *
       FROM have_translations;

SELECT *
FROM ru_missing_translations;

SELECT *
FROM ru_have_translations;


select *
    from ru_missing_translations rm JOIN ru_have_translations rh on rm.key = rh.key;# and rm.en_value = rh.en_value;


# SELECT *
# FROM ltm_translations lt JOIN on lt.id = st.id;

/* select all missing translations that have a pretty good match somewhere else*/
SELECT
    st.id,
    st.ru_value,
    st.en_value,
    ht.en_value,
    ht.ru_value
FROM
    ru_missing_translations st JOIN ru_have_translations ht
        ON st.en_value LIKE BINARY ht.en_value
ORDER BY st.id;

/* update all missing translations that have a pretty good match somewhere else*/
UPDATE ltm_translations lt
    JOIN (
             SELECT
                 st.id,
                 ht.ru_value
             FROM
                 ru_missing_translations st JOIN ru_have_translations ht
                     ON st.en_value LIKE BINARY ht.en_value
         ) tr ON tr.id = lt.id
SET lt.value = tr.ru_value, lt.status = 1;

/* select all missing translations that have a descent match somewhere else*/
SELECT
    st.id,
    st.ru_value,
    st.en_value,
    ht.en_value          ht_en_value,
    CAPITAL(ht.ru_value) ht_ru_value
FROM
    ru_missing_translations st JOIN ru_have_translations ht
        ON /*st.key = ht.key AND*/ st.en_value LIKE BINARY CAPITAL(ht.en_value)
ORDER BY st.id;

/* update all missing translations that have a pretty good match somewhere else*/
UPDATE ltm_translations lt
    JOIN (
             SELECT
                 st.id,
                 ht.ru_value
             FROM
                 ru_missing_translations st JOIN ru_have_translations ht
                     ON /*st.key = ht.key AND*/ st.en_value LIKE BINARY CAPITAL(ht.en_value)
         ) tr ON tr.id = lt.id
SET lt.value = CAPITAL(tr.ru_value), lt.status = 1;

SELECT
    st.id,
    st.ru_value,
    st.en_value,
    ht.en_value          ht_en_value,
    CAPITAL(ht.ru_value) ht_ru_value
FROM
    ru_missing_translations st JOIN ru_have_translations ht
        ON st.key = ht.key AND st.en_value LIKE CONCAT('%',ht.en_value,'%')
ORDER BY st.id;

/* update all missing translations that have a pretty good match somewhere else, ignoring case*/
UPDATE ltm_translations lt
    JOIN (
             SELECT
                 st.id,
                 ht.ru_value
             FROM
                 ru_missing_translations st JOIN ru_have_translations ht
                     ON st.key = ht.key AND st.en_value LIKE CONCAT('%',ht.en_value,'%')
         ) tr ON tr.id = lt.id
SET lt.value = CAPITAL(tr.ru_value), lt.status = 1;

DROP TEMPORARY TABLE IF EXISTS ru_missing_translations;
CREATE TEMPORARY TABLE ru_same_translations
    AS SELECT *
       FROM missing_translations;

DROP TEMPORARY TABLE IF EXISTS ru_have_translations;
CREATE TEMPORARY TABLE ru_have_translations
    AS SELECT *
       FROM have_translations;

SELECT *
FROM ru_missing_translations;

SELECT lt.* from ltm_translations lt
    JOIN ru_missing_translations st ON st.id = lt.id;

# finally, set the translations that are the same to NULL so they show up as missing
UPDATE ltm_translations lt
    JOIN ru_missing_translations st ON st.id = lt.id
SET lt.value = NULL, lt.status = 1;


COMMIT;


#select * from ltm_translations WHERE value = 'Адрес Электронной Почты';
#update ltm_translations set value = 'е-майл', status = 1 where value = 'Адрес Электронной Почты';

# select * from ltm_translations where value = 'е-майл' and `key` <> 'email';
# update ltm_translations set value = NULL, status = 1 where value = 'е-майл' and `key` <> 'email';


# TRUNCATE table ltm_translations;
