<?php

use Phinx\Migration\AbstractMigration;

class InitialMigration extends AbstractMigration
{

    public function up()
    {
        $this->table('task');
        $this->execute("CREATE TYPE task_status AS ENUM ('todo', 'done')");
        $this->execute("CREATE TABLE task (
            id serial primary key,
            user_name VARCHAR (64),
            email TEXT,
            text TEXT,
            status task_status DEFAULT 'todo',
            admin_fixed BOOLEAN DEFAULT FALSE
        )");
    }
}
