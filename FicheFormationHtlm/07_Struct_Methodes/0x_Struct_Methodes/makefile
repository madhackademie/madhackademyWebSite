# define the correct path and name
export PATH 		:= c:/raylib/w64devkit/bin:$(PATH)
RAYLIB_PATH         = c:/raylib/raylib
PROJECT_NAME        ?= GAMENAME
OUTDIR				= out/
SOURCE				= src/
DEBUGGING           ?= FALSE

CC = g++
MAKE = mingw32-make

ifeq ($(DEBUGGING), TRUE)
    CFLAGS += -g
else
    CFLAGS += -O2 -s -Wl,--gc-sections,--subsystem,windows
endif

CFLAGS += -Wall -std=c++20 -D_DEFAULT_SOURCE -Wno-missing-braces

INCLUDE_PATHS = -I.
LDFLAGS = -L.
LDLIBS = -lraylib -lopengl32 -lgdi32 -lcomdlg32 -lole32 -lwinmm

PROJECT_SOURCE_FILES ?= $(wildcard $(SOURCE)*.cpp $(SOURCE)**/*.cpp)

OBJS = $(PROJECT_SOURCE_FILES:.cpp=.o)

.PHONY: all

all:
	$(MAKE) $(PROJECT_NAME)
	$(OUTDIR)$(PROJECT_NAME)

$(PROJECT_NAME): $(OBJS)
	$(CC) -o $(OUTDIR)$(PROJECT_NAME)$(EXT) $(OBJS) $(CFLAGS) $(INCLUDE_PATHS) $(LDFLAGS) $(LDLIBS)

%.o: %.cpp
	$(CC) -c $< -o $@ $(CFLAGS) $(INCLUDE_PATHS)

clean:
	del $(subst /,\,$(OBJS))
	del .\out\*.exe
