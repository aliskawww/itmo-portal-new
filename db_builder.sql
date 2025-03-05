CREATE DATABASE it_portal DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(256) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'expert', 'user', 'consultant') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE professions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expert_id INT NOT NULL,
    profession_id INT NOT NULL,
    rating INT CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (profession_id) REFERENCES professions(id) ON DELETE CASCADE,
    UNIQUE (expert_id, profession_id)
);
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id),
    FOREIGN KEY (receiver_id) REFERENCES users(id)
);
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expert_id INT NOT NULL,
    profession_id INT NOT NULL,
    review TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (profession_id) REFERENCES professions(id) ON DELETE CASCADE
);

ALTER TABLE reviews ADD COLUMN rating INT CHECK (rating BETWEEN 1 AND 5);

ALTER TABLE reviews ADD UNIQUE (expert_id, profession_id);

INSERT INTO professions (name, description) VALUES
('Разработчик', 'Создает веб-сайты и приложения.'),
('Аналитик данных', 'Анализирует данные и строит модели.'),
('Системный администратор', 'Обеспечивает работу IT-инфраструктуры.');


CREATE TABLE pvk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Название ПВК',
    description TEXT COMMENT 'Описание ПВК'
);
CREATE TABLE profession_pvk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profession_id INT NOT NULL COMMENT 'ID профессии',
    pvk_id INT NOT NULL COMMENT 'ID ПВК',
    FOREIGN KEY (profession_id) REFERENCES professions(id) ON DELETE CASCADE,
    FOREIGN KEY (pvk_id) REFERENCES pvk(id) ON DELETE CASCADE
);
CREATE TABLE expert_pvk_lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expert_id INT NOT NULL COMMENT 'ID эксперта',
    profession_id INT NOT NULL COMMENT 'ID профессии',
    pvk_id INT NOT NULL COMMENT 'ID ПВК',
    priority INT NOT NULL COMMENT 'Приоритет ПВК в списке (1 - высший приоритет)',
    FOREIGN KEY (expert_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (profession_id) REFERENCES professions(id) ON DELETE CASCADE,
    FOREIGN KEY (pvk_id) REFERENCES pvk(id) ON DELETE CASCADE
);
CREATE TABLE profession_expert (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profession_id INT NOT NULL,
    expert_id INT NOT NULL,
    FOREIGN KEY (profession_id) REFERENCES professions(id),
    FOREIGN KEY (expert_id) REFERENCES users(id)
);
INSERT INTO pvk (name, description) VALUES
('Адекватная самооценка', 'Умение объективно оценивать свои силы и возможности.'),
('Самостоятельность', 'Способность действовать без посторонней помощи.'),
('Пунктуальность, педантичность', 'Точность и аккуратность в выполнении задач.'),
('Дисциплинированность', 'Следование правилам и порядку.'),
('Аккуратность', 'Стремление к чистоте и порядку.'),
('Организованность', 'Умение планировать и структурировать дела.'),
('Исполнительность', 'Добросовестное выполнение задач.'),
('Ответственность', 'Готовность отвечать за свои действия.'),
('Трудолюбие', 'Усердие в работе.'),
('Инициативность', 'Способность предлагать идеи и действовать самостоятельно.'),
('Самокритичность', 'Умение объективно оценивать свои ошибки.'),
('Оптимизм', 'Позитивный взгляд на жизнь.'),
('Самообладание', 'Способность контролировать эмоции.'),
('Самоконтроль', 'Умение управлять своим поведением.'),
('Предусмотрительность', 'Способность предвидеть последствия.'),
('Уверенность в себе', 'Вера в свои силы.'),
('Тайм-менеджмент', 'Умение управлять временем.'),
('Стрессоустойчивость', 'Способность сохранять спокойствие в сложных ситуациях.'),
('Гибкость', 'Умение адаптироваться к изменениям.'),
('Решительность', 'Способность принимать решения.'),
('Сильная воля', 'Умение добиваться целей.'),
('Смелость', 'Готовность к риску.'),
('Чувство долга', 'Ответственность перед другими.'),
('Честность', 'Правдивость в словах и поступках.'),
('Порядочность', 'Следование моральным принципам.'),
('Товарищество', 'Умение поддерживать других.'),
('Креативность', 'Способность к творчеству.'),
('Оперативность', 'Быстрое выполнение задач.'),
('Образное представление', 'Умение визуализировать.'),
('Абстрактное представление', 'Способность мыслить абстрактно.'),
('Пространственное воображение', 'Умение представлять объекты в пространстве.'),
('Зрительная память', 'Способность запоминать зрительные образы.'),
('Слуховая память', 'Умение запоминать звуки и речь.'),
('Тактильная память', 'Способность запоминать ощущения.'),
('Энергичность', 'Активность и жизненная сила.'),
('Умственная работоспособность', 'Способность к интеллектуальному труду.'),
('Физическая работоспособность', 'Способность к физическому труду.'),
('Нервно-эмоциональная устойчивость', 'Устойчивость к стрессу.'),
('Выносливость', 'Способность переносить нагрузки.'),
('Внимательность', 'Умение сосредотачиваться на деталях.');
INSERT INTO profession_pvk (profession_id, pvk_id) VALUES
(1, (SELECT id FROM pvk WHERE name = 'Самостоятельность')),
(1, (SELECT id FROM pvk WHERE name = 'Пунктуальность, педантичность')),
(1, (SELECT id FROM pvk WHERE name = 'Дисциплинированность')),
(1, (SELECT id FROM pvk WHERE name = 'Организованность')),
(1, (SELECT id FROM pvk WHERE name = 'Исполнительность')),
(1, (SELECT id FROM pvk WHERE name = 'Ответственность')),
(1, (SELECT id FROM pvk WHERE name = 'Трудолюбие')),
(1, (SELECT id FROM pvk WHERE name = 'Самокритичность')),
(1, (SELECT id FROM pvk WHERE name = 'Самоконтроль')),
(1, (SELECT id FROM pvk WHERE name = 'Тайм-менеджмент')),
(1, (SELECT id FROM pvk WHERE name = 'Стрессоустойчивость')),
(1, (SELECT id FROM pvk WHERE name = 'Гибкость')),
(1, (SELECT id FROM pvk WHERE name = 'Решительность')),
(1, (SELECT id FROM pvk WHERE name = 'Товарищество')),
(1, (SELECT id FROM pvk WHERE name = 'Креативность')),
(1, (SELECT id FROM pvk WHERE name = 'Оперативность')),
(1, (SELECT id FROM pvk WHERE name = 'Зрительная память')),
(1, (SELECT id FROM pvk WHERE name = 'Умственная работоспособность')),
(1, (SELECT id FROM pvk WHERE name = 'Внимательность'));
INSERT INTO profession_pvk (profession_id, pvk_id) VALUES
(2, (SELECT id FROM pvk WHERE name = 'Самостоятельность')),
(2, (SELECT id FROM pvk WHERE name = 'Пунктуальность, педантичность')),
(2, (SELECT id FROM pvk WHERE name = 'Дисциплинированность')),
(2, (SELECT id FROM pvk WHERE name = 'Организованность')),
(2, (SELECT id FROM pvk WHERE name = 'Исполнительность')),
(2, (SELECT id FROM pvk WHERE name = 'Ответственность')),
(2, (SELECT id FROM pvk WHERE name = 'Трудолюбие')),
(2, (SELECT id FROM pvk WHERE name = 'Инициативность')),
(2, (SELECT id FROM pvk WHERE name = 'Самокритичность')),
(2, (SELECT id FROM pvk WHERE name = 'Самообладание')),
(2, (SELECT id FROM pvk WHERE name = 'Самоконтроль')),
(2, (SELECT id FROM pvk WHERE name = 'Тайм-менеджмент')),
(2, (SELECT id FROM pvk WHERE name = 'Стрессоустойчивость')),
(2, (SELECT id FROM pvk WHERE name = 'Гибкость')),
(2, (SELECT id FROM pvk WHERE name = 'Решительность')),
(2, (SELECT id FROM pvk WHERE name = 'Креативность')),
(2, (SELECT id FROM pvk WHERE name = 'Оперативность')),
(2, (SELECT id FROM pvk WHERE name = 'Абстрактное представление')),
(2, (SELECT id FROM pvk WHERE name = 'Зрительная память')),
(2, (SELECT id FROM pvk WHERE name = 'Умственная работоспособность')),
(2, (SELECT id FROM pvk WHERE name = 'Внимательность'));
INSERT INTO profession_pvk (profession_id, pvk_id) VALUES
(3, (SELECT id FROM pvk WHERE name = 'Дисциплинированность')),
(3, (SELECT id FROM pvk WHERE name = 'Ответственность')),
(3, (SELECT id FROM pvk WHERE name = 'Внимательность')),
(3, (SELECT id FROM pvk WHERE name = 'Стрессоустойчивость')),
(3, (SELECT id FROM pvk WHERE name = 'Организованность')),
(3, (SELECT id FROM pvk WHERE name = 'Исполнительность')),
(3, (SELECT id FROM pvk WHERE name = 'Тайм-менеджмент')),
(3, (SELECT id FROM pvk WHERE name = 'Гибкость')),
(3, (SELECT id FROM pvk WHERE name = 'Решительность')),
(3, (SELECT id FROM pvk WHERE name = 'Предусмотрительность')),
(3, (SELECT id FROM pvk WHERE name = 'Умственная работоспособность')),
(3, (SELECT id FROM pvk WHERE name = 'Нервно-эмоциональная устойчивость')),
(3, (SELECT id FROM pvk WHERE name = 'Креативность')),
(3, (SELECT id FROM pvk WHERE name = 'Оперативность'));
