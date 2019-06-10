create view shift_details as 
SELECT n.name, s.service, b.aircraft, b.aircrafttype, sr.shift_date, sr.location 
FROM ns_customers n, services s, shift_budget b , shift_report sr 
WHERE b.shift_report_id = sr.shift_report_id and n.ns_customer_id = b.ns_customer_id AND s.service_id = b.service_id 
order by sr.shift_date desc




Create view department_location as SELECT distinct location,department FROM nas.budget_data_06_09;