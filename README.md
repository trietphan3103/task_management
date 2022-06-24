# FINAL PROJECT WEB

# Documentation:

## 1) Run server:

-   As my group have 1 person using Linux OS and seems like Linux does not aprrove service with different network to communicate with each other, so we have to declare a new network to put these service in the same network, sorry for any inconvenient

-   Run command:
```
    docker network create web_final
    docker-compose up -d
```

-   Your website has now running on port: 8080

-   Sql server's port: 3399:3306

-   Phpadmin's port: 8888

-   As this step, please wait till the Sql server has started or else out website won't work correctly

## 2) Default user:

-   Admin (username/password): admin/admin

-   Manager 1: minhtriet/minhtriet

-   Manager 2: thanghy/thanghy

-   Manager 3: dangtri/dangtri

-   Employee: hoaibao/hoaibao

-   Server will make you to change the default password, change password to continue

## 3) Feature:

-   Sign up/Sign in/Sign out

-   Task management (For task view, when the media screen width is too small, task column can be horizontal scroll)

-   Absence management

-   User management

-   And other features

## 4) Testing instruction:

-   Please login as dangtri to test manager, hoaibao to test employee

-   Login with normal employee for testing update avatar, update password

-   Please login with user admin to test manage user, manage absence for others manager, reset default password for employee, department management

-   You can open 2 tab to sign in 2 account for testing

-   Use manager account to create an absence and then you can approved or reject the absence in admin page, if the absence is approved, the bank holiday of user will be updated immediately. The same for normal employee and manager

-   Use manager account to create a new task and assign it to any user that in the same department. Use employee account to start a task. Employee can start task if only they are in the same department and the task is assigned to this employee

-   After employee start the task, submit the task and then the task will be update to "waiting" stage

-   Use manager account to approve or reject the task, if the task is rejected the status will be updated to "rejected", otherwise "aprroved" -> After rejected or approved, task's history will be created immediately

-   When the task is rejected, employee can assign issue again and submit the task again and continue it's life cycle.
## 5) Team member:

#### This project is contributed by:

-   51900715 - Dang Dang Tri

-   51900750 - Xin Thang Hy

-   51900778 - Phan Minh Triet