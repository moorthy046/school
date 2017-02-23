## School management system in Laravel 5.2

Management system for all types of educational institutions like schools and colleges.

###Requirements
* Its always recommended to use vps or an environment where composer is available for laravel applications Before purchasing, please make sure your server has composer installed and has at least php 5.5.9 and below requirements from Laravel apply
* PHP >= 5.5.9
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension

**For install use custom install process that integrate into SMS system**

**During installation you can select multi school system or not**

Integrates and facilitates 8 types of user accounts of a school :

* [Super Administrator](#superadministrator)
* [Administrator](#administrator)
* [Human resurces](#humanresources)
* [Librarian](#librarian)
* [Teacher](#teacher)
* [Student](#student)
* [Parent](#parent)
* [Visitor](#visitor)

Features:

###Super Administrator
* Add / edit / delete schools
* Add / edit / delete school admins
* Add / edit / delete school years
* Add / edit / delete semesters
* Add / edit / delete directions
* Add / edit / delete subjects
* Add / edit / delete mark type
* Add / edit / delete mark value
* Add / edit / delete notice type
* See registered visitors
* See login history
* Add / edit / delete static pages
* Add / edit / delete certificate
* Set up settings
* Add / edit / delete schools options
* Sent message to any user in system
* Paypal email in system settings is paypal payment gateway for student invoice online payments
* Manage own profile
* View own certificate
* Access account from anywhere, by any device like desktop, laptop, smart phone and tablet

###Administrator
* Manage students class/group wise
* Add / edit / delete student
* View profile of students
* Manage teacher profile
* Add / edit / delete teacher information
* Manage parent according to student class wise
* Create / edit / delete sections / group for students
* Subjects can be defined separately according to each classes
* Manage class routine
* Create / edit / delete class routine schedule on 7days a week
* Manage payment for student
* Create / edit / delete parents
* Create / edit / delete human resources
* See registered visitors
* Create / edit / delete invoice listing
* View invoice and print them
* Manage transportation routes for school
* Manage dormitory listing for school
* Manage noticeboard of school
* Menage messages for school users
* Create / edit / delete notices according to date
* Notices are visible in calendar in dashboard
* Add certificate to users
* Send SMS message to any user who had a mobile phone
* View teacher diaries
* Edit system settings
* View own certificate
* Manage own profile
* Access account from anywhere, by any device like desktop, laptop, smart phone and tablet

###Human Resources
* Create / edit / delete parents
* Create / edit / delete students
* Create / edit / delete librarian
* Create / edit / delete teachers
* View own certificate
* Manage own profile
* Access account from anywhere, by any device like desktop, laptop, smart phone and tablet

###Librarian
* Manage library
* Create / edit / delete book list
* Issue book to any user
* Return book from any user
* See reserved books from any user
* View own certificate
* Manage own profile
* Access account from anywhere, by any device like desktop, laptop, smart phone and tablet
* **RESTful API for this role**

###Teacher
* Manage students class/group wise
* Add / edit / delete diaries
* View profile of students
* View mark sheet of student
* View teacher profile
* Manage exam / semester listing
* Manage marks (edit/ update) and attendance exam,class & student wise
* View class routine
* View library and book status
* View school transportation routes status
* View / edit noticeboard or school events
* View own certificate
* Manage own profile
* Access account from anywhere, by any device like desktop, laptop, smart phone and tablet
* **RESTful API for this role**

###Student
* View own class subjects
* View teacher diaries
* View own marks and attendances
* View class routine
* View invoice and payment list
* View library and book status
* Book reservation
* View school transportation and routes status
* View dormitory listing and their status
* View noticeboard and school events in calendar
* View own certificate
* Manage own profile
* View own student card
* Access account from anywhere, by any device like desktop, laptop, smart phone and tablet
* **RESTful API for this role**

###Parent
* View own children marks and attendances and other comments from teacher
* View own children class routine
* View teacher diaries
* View own children invoice and payment list
* Make online or offline payment
* Online payment can be paid via [paypal]
* View library and book status
* View school transportation and routes status
* View dormitory listing and their status
* View noticeboard and school events in calendar
* View own certificate
* Manage own profile
* Access account from anywhere, by any device like desktop, laptop, smart phone and tablet
* **RESTful API for this role**

###Visitor
* View own visitor card
* Manage own profile
* Access account from anywhere, by any device like desktop, laptop, smart phone and tablet