cmake_minimum_required(VERSION 3.10)
project(computation)

set(CMAKE_CXX_STANDARD 11)

set(MYSQLCONNECTOR_PATH "C:/Program Files/MySQL/MySQL Connector C++ 1.1.11")
include_directories(${MYSQLCONNECTOR_PATH}/include)
include_directories(${MYSQLCONNECTOR_PATH}/include/cppconn)
link_directories(${MYSQLCONNECTOR_PATH}/lib/opt)
add_executable(main main.cpp)
target_link_libraries(main mysqlcppconn)