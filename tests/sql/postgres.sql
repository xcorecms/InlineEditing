SET search_path = public;

SET default_tablespace = '';

SET default_with_oids = false;

DROP TABLE IF EXISTS inline_content;

CREATE TABLE inline_content (
    id integer NOT NULL,
    namespace character varying NOT NULL,
    locale character(2) NOT NULL,
    name character varying NOT NULL,
    content text NOT NULL
);


ALTER TABLE inline_content OWNER TO postgres;

CREATE SEQUENCE inline_content_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE inline_content_id_seq OWNER TO postgres;
ALTER SEQUENCE inline_content_id_seq OWNED BY inline_content.id;
ALTER TABLE ONLY inline_content ALTER COLUMN id SET DEFAULT nextval('inline_content_id_seq'::regclass);
SELECT pg_catalog.setval('inline_content_id_seq', 1, false);
ALTER TABLE ONLY inline_content ADD CONSTRAINT inline_content_id PRIMARY KEY (id);
ALTER TABLE ONLY inline_content ADD CONSTRAINT inline_content_unique UNIQUE (namespace, locale, name);
CREATE INDEX inline_content_index ON inline_content USING btree (namespace, locale, name);
