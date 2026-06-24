#include "raylib.h"
#include <cstdio>

int main(void)
{
    // create a windows for the app with size and title as param
    InitWindow(800, 450, "raylib [core] example - basic window");
    int count = 0;
    while (count < 5)
    {
        count++;
        printf("while count : %i\n",count);
    }
    count = 0;
    do 
    {
        count++;
        printf("do while count : %i\n",count);
    }while (count < 5);
    int array[10]{};
    for (int i = 0; i < 10; i++)
    {
        array[i] = i + 1;
        printf("value array[pos : %i] = %i\n",i,array[i]);
    }
    // i = 1
    // for (int i = 1; i <= 10; i++)
    // {
    //     array[i] = i;
    //     printf("array[pos : %i] = %i\n",i,array[i]);
    // }
    
    for (auto &&i : array)
    {
        printf("foreach array value : %i\n",i);
    }
    
    while (!WindowShouldClose())
    {
        BeginDrawing();
        // need to clear the background every frame see the tutorial on the hello world section
        ClearBackground(RAYWHITE);

        for (int i = 0; i < 10; i++)
        {
            DrawText(TextFormat("array [pos : %i] = %i",i,array[i]),50,50 + i *30,25,GOLD);
        }
        //i = 1
        // for (int i = 1; i <= 10; i++)
        // {
        //     DrawText(TextFormat("array [pos : %i] = %i",i,array[i]),50,50 + i *30,25,GOLD);
        // }
        EndDrawing();
    }
    CloseWindow();

    return 0;
}