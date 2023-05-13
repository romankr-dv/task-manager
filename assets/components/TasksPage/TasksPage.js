import React, {useLayoutEffect, useState} from 'react';
import {useParams} from "react-router-dom";
import Config from "./../App/Config";
import Helper from "./../App/Helper";
import TaskListWrapper from "./TaskListWrapper/TaskListWrapper";
import TaskPanelHeading from "./TaskPanelHeading/TaskPanelHeading";
import Page from "../App/Page";
import PanelBody from "../App/PanelBody/PanelBody";
import TaskPanelFooter from "./TaskPanelFooter/TaskPanelFooter";
import TaskReminderCalendar from "./TaskReminderCalendar/TaskReminderCalendar";
import LocalStorage from "../App/LocalStorage";

const TasksPage = ({title, icon, fetchFrom, nested}) => {

  const findRootTask = (params) => {
    if (!params.root || !params.root.match(new RegExp('^[0-9]+$'))) {
      return null;
    }
    return {id: parseInt(params.root)};
  }
  const composeRootTask = (root, previousRoot, tasks) => {
    if (!root) {
      return null;
    }
    return {...root, ...previousRoot, ...tasks?.find(task => task.id === root.id)};
  }
  const checkRootTask = (task, root, tasks) => {
    if (task.parent === null) {
      return false;
    }
    if (task.parent === root.id) {
      return true;
    }
    return checkRootTask(tasks.find(parent => parent.id === task.parent), root, tasks);
  }
  const isTaskVisible = (task, search, tasks, root) => {
    if (nested && root && !checkRootTask(task, root, tasks)) {
      return false;
    }
    if (task.title.toLowerCase().includes(search.toLowerCase())) {
      return true;
    }
    if (task.link && Helper.isGithubLink(task.link) && Helper.getGithubIssueNumber(task.link).includes(search)) {
      return true;
    }
    return tasks.find(child => child.parent === task.id && isTaskVisible(child, search, tasks, root)) !== undefined;
  }

  const params = useParams();
  const [root, setRoot] = useState(findRootTask(params))
  const [tasks, setTasks] = useState(undefined);
  const [showCalendar, setShowCalendar] = useState(LocalStorage.getShowCalendar());
  const [statuses, setStatuses] = useState(undefined);
  const [search, setSearch] = useState("");
  const [activeTask, setActiveTask] = useState(undefined);
  const [reminderNumber, setReminderNumber] = useState(LocalStorage.getReminderNumber());

  const events = new function () {
    return {
      reload: () => {
        setTasks([]);
        Helper.fetchJson(fetchFrom)
          .then(response => {
            const newRoot = findRootTask(params)
            const tasks = response.tasks.map(task => {
              task.isHidden = !isTaskVisible(task, search, response.tasks, newRoot);
              return task;
            });
            setStatuses(response.statuses);
            setActiveTask(response.activeTask);
            setTasks(tasks);
            setRoot(composeRootTask(newRoot, root, tasks));
            setReminderNumber(response.reminderNumber);
          });
      },
      updateTask: (id, update) => {
        setTasks(tasks => {
          return tasks.map(task => {
            if (task.id === id) {
              task = {...task, ...update};
            }
            return task;
          });
        })
      },
      createNewTask: (parent = null) => {
        Helper.fetchNewTask(parent)
          .then(task => {
            task.autoFocus = true;
            setTasks(tasks => [task, ...tasks])
            if (parent !== null) {
              events.updateTask(parent, {isChildrenOpen: true})
            }
          });
      },
      startTask: (id) => {
        Helper.fetchTaskStart(id)
          .then(response => setActiveTask({task: id, trackedTime: 0, path: response.activeTask.path}));
      },
      finishTask: (id) => {
        Helper.fetchTaskFinish(id)
          .then(() => setActiveTask(undefined));
      },
      removeTask: (id) => {
        const task = tasks.find(task => task.id === id);
        if (!confirm("Are you sure, you want to remove '" + task.title + "'?")) {
          return;
        }
        Helper.fetchTaskDelete(id)
          .then(() => {
            // todo: remove task children
            setTasks(tasks => tasks.filter(i => i.id !== id))
          })
      },
      updateTaskTitle: (id, title, setTitleChanging) => {
        setTitleChanging(true);
        events.updateTask(id, {title: title});
        Helper.addTimeout('task_title' + id, () => {
          Helper.fetchTaskEdit(id, {'title': title})
            .then(() => setTitleChanging(false));
        }, Config.updateInputTimeout);
      },
      updateTaskLink: (id, link, setLinkChanging) => {
        setLinkChanging(true);
        events.updateTask(id, {link: link});
        Helper.addTimeout('task_link' + id, () => {
          Helper.fetchTaskEdit(id, {'link': link})
            .then(() => setLinkChanging(false));
        }, Config.updateInputTimeout);
      },
      updateTaskReminder: (task, reminder) => {
        const time = Math.floor(Date.now() / 1000);
        const taskWasReminder = task.reminder && task.reminder < time;
        const taskWillBeReminder = reminder && reminder < time;

        if (taskWasReminder && !taskWillBeReminder) setReminderNumber(reminderNumber - 1);
        if (!taskWasReminder && taskWillBeReminder) setReminderNumber(reminderNumber + 1);

        events.updateTask(task.id, {reminder: reminder});
        Helper.fetchTaskEdit(task.id, {'reminder': reminder}).then();
      },
      updateTaskStatus: (id, status) => {
        events.updateTask(id, {status: status});
        Helper.fetchTaskEdit(id, {'status': status}).then();
      },
      updateTaskChildrenViewSetting: (id, value) => {
        events.updateTask(id, {isChildrenOpen: value})
      },
      updateTaskAdditionalPanelViewSetting: (id, value) => {
        events.updateTask(id, {isAdditionalPanelOpen: value})
      },
      updateTaskDescription: (id, description, setDescriptionChanging) => {
        setDescriptionChanging(true);
        events.updateTask(id, {description: description})
        Helper.addTimeout('task_description' + id, () => {
          Helper.fetchTaskEdit(id, {'description': description})
            .then(() => setDescriptionChanging(false));
        }, Config.updateInputTimeout);
      },
      toggleCalendar: () => {
        setShowCalendar(!showCalendar);
      },
      onSearchUpdate: () => {
        setTasks((tasks) => tasks.map(task => {
          task.isHidden = !isTaskVisible(task, search, tasks, root);
          task.autoFocus = false;
          return task;
        }));
      },
      onRootUpdate: () => {
        const newRoot = findRootTask(params);
        setTasks((tasks) => tasks.map(task => {
          task.isHidden = !isTaskVisible(task, search, tasks, newRoot);
          task.autoFocus = false;
          return task;
        }));
        setRoot(composeRootTask(newRoot, root, tasks))
      },
      onReminderNumberUpdate: () => {
        LocalStorage.setReminderNumber(reminderNumber);
      },
      onShowCalendarUpdate: () => {
        LocalStorage.setShowCalendar(showCalendar);
      },
    }
  }

  useLayoutEffect(events.reload, [fetchFrom]);
  useLayoutEffect(events.onSearchUpdate, [search]);
  useLayoutEffect(events.onReminderNumberUpdate, [reminderNumber]);
  useLayoutEffect(events.onShowCalendarUpdate, [showCalendar]);
  useLayoutEffect(events.onRootUpdate, [params.root]);

  return (
    <Page sidebar={{root: root, onSearch: setSearch, reminderNumber: reminderNumber}}>
      <TaskPanelHeading title={title} icon={icon} root={root} events={events}/>
      {showCalendar ? <TaskReminderCalendar tasks={tasks} statuses={statuses} events={events}/> : null}
      <PanelBody>
        <TaskListWrapper data={{
          root: root,
          tasks: tasks,
          activeTask: activeTask,
          statuses: statuses,
          nested: nested
        }} events={events}/>
        <TaskPanelFooter tasks={tasks}/>
      </PanelBody>
    </Page>
  );
}

export default TasksPage;
