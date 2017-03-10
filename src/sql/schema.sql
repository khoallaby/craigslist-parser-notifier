
CREATE TABLE IF NOT EXISTS cl_jobs (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    pid bigint(20) unsigned NOT NULL,
    city_id int(11) NOT NULL,
    date datetime DEFAULT NULL,
    date_modified datetime DEFAULT NULL,
    cat varchar(4) DEFAULT NULL,
    link varchar(255) DEFAULT NULL,
    title varchar(255) DEFAULT NULL,
    description text,
    notify TINYINT(1) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY (pid)
);

CREATE TABLE IF NOT EXISTS cl_regions (
    region_id int(11) NOT NULL,
    name varchar(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS cl_states (
    state_id int(11) NOT NULL,
    region_id int(11) NOT NULL,
    state_code varchar(10) NOT NULL,
    name varchar(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS cl_cities (
    city_id int(11) NOT NULL,
    state_id int(11) NOT NULL,
    code varchar(255) NOT NULL,
    url varchar(255) NOT NULL,
    name varchar(255) NOT NULL
);



/*
ALTER TABLE cl_states ADD CONSTRAINT states_region_fkey FOREIGN KEY (region_id) REFERENCES cl_regions(region_id);
ALTER TABLE cl_cities ADD CONSTRAINT city_state_fkey FOREIGN KEY (state_id) REFERENCES cl_states(state_id);
*/

