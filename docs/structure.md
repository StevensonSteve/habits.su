user:
- id
- status // Статус учетной записи (активна/неактивна/заблокирована)
- email // Адрес электронной почты для входа в систему
- password // Захешированный пароль пользователя
- role_id // (FK role.id) ManyToOne
- createdAt
- updatedAt

user_info:
- id
- user_id // (FK user.id) OneToOne with user
- firstName // Имя пользователя
- lastName // Фамилия пользователя
- patronymic // Отчество пользователя
- address // Физический адрес проживания
- phone_number // Контактный телефон
- passport_number // Номер паспорта
- birth_date // Дата рождения
- createdAt
- updatedAt

role:
- id
- name // Наименование роли (Администратор, Менеджер, Водитель и т.д.)
- description // Описание роли и ее полномочий
- createdAt
- updatedAt

action:
- id
- name // Наименование действия (создание, чтение, обновление, удаление)
- code // Уникальный код действия для программирования
- description // Описание действия
- createdAt
- updatedAt

role_action:
- role_id // (FK role.id) ManyToMany
- action_id // (FK action.id) ManyToMany
- createdAt

truck:
- id
- vin // Идентификационный номер транспортного средства
- brand // Марка грузовика
- model // Модель грузовика
- manufacture_date // Дата производства
- mileage_initial // начальный пробег
- engine_type // Тип двигателя (дизельный, бензиновый, электрический)
- engine_capacity // Мощность двигателя в лошадиных силах
- engine_volume // Объем двигателя в литрах
- purchase_date // Дата покупки
- color // Цвет
- license_plate // Государственный номерной знак
- max_weight // Максимальная грузоподъемность
- empty_weight // Собственный вес без груза
- description // Дополнительное описание и характеристики
- updatedAt
- createdAt

trailer:
- id
- vin // Идентификационный номер прицепа
- brand // Марка прицепа
- model // Модель прицепа
- manufacture_date // Дата производства
- purchase_date // Дата покупки
- color // Цвет
- license_plate // Государственный номерной знак
- max_weight // Максимальная грузоподъемность
- empty_weight // Собственный вес без груза
- description // Дополнительное описание
- updatedAt
- createdAt

expense_category: ('vehicle', 'purchase', 'tax', 'rent', 'office', 'other')
- id
- name // Наименование категории расходов
- description
- createdAt
- updatedAt

expense:
- id
- name // Короткое описание
- amount // Сумма расхода
- description // Описание расхода
- expense_date // Дата осуществления расхода
- category_id // (FK expense_category.id)
- resource_id // ID связанного ресурса (user_id, vehicle_id и т.д.) - NULLABLE
- resource_type // тип связанного ресурса - NULLABLE
- additional_data // JSON с дополнительными данными
- createdAt
- updatedAt

salary_payment:
- id
- expense_id // (FK expense.id) OneToOne - ссылка на основную запись расхода
- user_id // (FK user.id) ManyToOne
- salary_period // период начисления (месяц, год)
- salary_type // тип зарплаты (оклад, аванс, премия, отпускные)
- hours_worked // отработанные часы
- bonus_amount // сумма премии
- deduction_amount // сумма удержаний
- net_amount // итоговая сумма к выплате
- payment_date // дата выплаты
- payment_status // статус выплаты (начислено, выплачено, задержано)
- createdAt
- updatedAt

todo_task:
- id
- user_id // (FK user.id) ManyToOne
- status // Статус задачи
- done // Флаг выполнения задачи
- title // Заголовок задачи
- description // Описание задачи
- createdAt
- updatedAt

client:
- id
- name // Наименование клиента/компании
- contact_person // Контактное лицо
- phone // Телефон для связи
- email // Электронная почта
- address // Юридический адрес
- tax_number // ИНН/налоговый номер
- payment_terms // Условия оплаты
- createdAt
- updatedAt

shipment:
- id
- client_order_id // (FK client_order.id) ManyToOne
- shipment_number // Уникальный номер рейса (id уже есть?)
- status // ENUM('planned', 'in_progress', 'completed', 'cancelled')
- total_distance // Общее расстояние рейса в км
- total_weight // Общий вес груза в кг (не выглядит нужным)
- total_volume // Общий объем груза в м³ (не выглядит нужным)
- planned_start_date // Планируемая дата начала
- planned_end_date // Планируемая дата окончания
- actual_start_date // Фактическая дата начала
- actual_end_date // Фактическая дата окончания
- createdAt
- updatedAt

shipment_vehicle:
- id
- shipment_id // (FK shipment.id) ManyToOne
- truck_id // (FK truck.id) ManyToOne
- trailer_id // (FK trailer.id) ManyToOne - NULLABLE
- driver_id // (FK user.id) ManyToOne
- start_mileage // пробег на начало рейса
- end_mileage // пробег на конец рейса
- start_fuel_level // уровень топлива на начало рейса
- end_fuel_level // уровень топлива на конец рейса
- fuel_consumption // расход топлива за рейс
- average_speed // средняя скорость
- max_speed // максимальная скорость
- engine_hours // моточасы
- driver_comment // комментарий водителя
- vehicle_condition // состояние ТС после рейса
- createdAt
- updatedAt

route_point:
- id
- shipment_id // (FK shipment.id) ManyToOne
- point_type // ENUM('loading', 'unloading', 'transit')
- sequence_number // Порядковый номер точки в маршруте
- address // Физический адрес точки
- contact_person // Контактное лицо в точке
- phone // Телефон контактного лица
- planned_arrival // Планируемое время прибытия
- planned_departure // Планируемое время отправления
- actual_arrival // Фактическое время прибытия
- actual_departure // Фактическое время отправления
- distance_from_previous // Расстояние от предыдущей точки
- createdAt
- updatedAt

cargo:
- id
- shipment_id // (FK shipment.id) ManyToOne (надо ли оно или млжно через 2 модели доставать)
- route_point_id // (FK route_point.id) ManyToOne - точка загрузки/выгрузки
- description // Описание груза
- cargo_type // Тип груза
- weight // Вес в кг
- volume // Объем в м³
- dimensions // JSON {length, width, height}
- packaging_type // Тип упаковки
- hazardous_material // Признак опасного груза
- temperature_requirements // Температурный режим
- handling_instructions // Инструкции по обращению
- createdAt
- updatedAt

client_order:
- id
- client_id // (FK client.id) ManyToOne
- order_number // Номер заказа (id уже есть?)
- order_date // Дата заказа
- status // ENUM('new', 'confirmed', 'in_progress', 'completed', 'cancelled')
- total_amount // Общая сумма заказа
- payment_status // Статус оплаты
- createdAt
- updatedAt

order_shipment:
- id
- order_id // (FK client_order.id) ManyToOne
- shipment_id // (FK shipment.id) OneToOne ???
- createdAt
- updatedAt

work_category:
- id
- name // Категория работ (двигатель, трансмиссия, электроника, кузов и т.д.)
- description
- createdAt
- updatedAt

work_type:
- id
- category_id // (FK work_category.id) ManyToOne
- name // Конкретный вид работ
- description
- standard_time // Нормативное время выполнения
- complexity // Сложность работ
- vehicle_type // ENUM('truck', 'trailer', 'both')
- createdAt
- updatedAt

maintenance_request(work_repair):
- id
- vehicle_type // ENUM('truck', 'trailer')
- vehicle_id // (FK truck.id или trailer.id)
- requested_by // (FK user.id) ManyToOne - кто подал заявку
- request_date // Дата заявки
- problem_description // Описание проблемы
- urgency // Срочность (обычная, срочная, аварийная)
- status // ENUM('new', 'approved', 'in_progress', 'completed', 'rejected')
- createdAt
- updatedAt

work_performed:
- id
- request_id // (FK maintenance_request.id) ManyToOne - NULLABLE
- work_type_id // (FK work_type.id) ManyToOne
- mechanic_id // (FK user.id) ManyToOne - исполнитель
- start_time // Время начала работ
- end_time // Время окончания работ
- actual_time // Фактическое время в часах
- expense_id // (FK expense.id) OneToOne - связь с затратами
- tools_used // Использованные инструменты
- materials_used // Использованные материалы
- work_description // Описание выполненных работ
- quality_check // Результат проверки качества
- signed_by // (FK user.id) ManyToOne - кто принял работу
- createdAt
- updatedAt

scheduled_maintenance:
- id
- vehicle_type // ENUM('truck', 'trailer')
- vehicle_id // (FK truck.id или trailer.id)
- work_type_id // (FK work_type.id) ManyToOne
- schedule_type // ENUM('by_mileage', 'by_time', 'by_engine_hours')
- interval_value // Интервал (км, дни, моточасы)
- last_performed // Когда выполнялось последний раз
- next_due // Когда due следующее выполнение
- is_active // Активно ли напоминание
- createdAt
- updatedAt
