#include <iostream>
#include "Models/Word.cpp"
#include "mysql_connection.h"

using namespace std;

int main() {
    cout << "Hello, World!" << endl;
    Word wordObj;

    wordObj.category_id = 1;

    cout << "Category ID: " << wordObj.category_id << endl;

    return 0;
}

