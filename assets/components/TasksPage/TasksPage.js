import React, {useLayoutEffect, useState} from 'react';
import {useParams} from "react-router-dom";
import Config from "./../App/Config";
import Helper from "./../App/Helper";
import TaskPanelHeading from "./TaskPanelHeading/TaskPanelHeading";
import Page from "../App/Page";
import PanelBody from "../App/PanelBody/PanelBody";
import TaskReminderCalendar from "./TaskReminderCalendar/TaskReminderCalendar";
import LocalStorage from "../App/LocalStorage";
import TaskList from "./TaskList/TaskList";
import LazyLoading from "../App/Common/LazyLoading";

const TasksPage = ({title, icon, fetchFrom}) => {
  const params = useParams();
  const [root, setRoot] = useState(undefined)
  const [tasks, setTasks] = useState([]);
  const [startFrom, setStartFrom] = useState(undefined)
  const [showCalendar, setShowCalendar] = useState(LocalStorage.getShowCalendar());
  const [statuses, setStatuses] = useState(undefined);
  const [search, setSearch] = useState(undefined);
  const [reminderNumber, setReminderNumber] = useState(LocalStorage.getReminderNumber());
  const [fetching, setFetching] = useState(false);

  const events = new function () {
    return {
      fetch: () => {
        if (!fetching) {
          setFetching(true);
          Helper.fetchJson(fetchFrom, {'parent': params.root, 'search': search})
            .then(response => {
              setStatuses(response.statuses);
              setTasks(response.tasks);
              setRoot(response.parent);
              setReminderNumber(response.reminderNumber);
              setStartFrom(response.startFrom);
              setFetching(false);
            });
        }
      },
      loadMore: () => {
        if (!fetching && startFrom) {
          setFetching(true);
          Helper.fetchJson(fetchFrom, {'parent': params.root, 'search': search, 'startFrom': startFrom})
            .then(response => {
              setTasks([...tasks, ...response.tasks]);
              setStartFrom(response.startFrom);
              setFetching(false);
            });
        }
      },
      reload: () => {
        setTasks([]);
        events.fetch();
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
          });
      },
      removeTask: (id) => {
        const task = tasks.find(task => task.id === id);
        if (!confirm("Are you sure, you want to remove '" + task.title + "'?")) {
          return;
        }
        Helper.fetchTaskDelete(id)
          .then(() => {
            // todo: remove task children
            setTasks(tasks => tasks.filter(task => task.id !== id))
          })
      },
      updateTaskPriority: (id) => {
        Helper.fetchTaskPriorityUpdate(id)
          .then(() => {
            window.scrollTo({ top: 0, left: 0})
            setTasks(tasks => {
              const task = tasks.find(task => task.id === id)
              tasks = tasks.filter(task => task.id !== id);
              tasks.unshift(task)
              return tasks
            })
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
      updateTaskControlPanelView: (id, value) => {
        events.updateTask(id, {isTaskControlPanelOpen: value})
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
      onReminderNumberUpdate: () => {
        LocalStorage.setReminderNumber(reminderNumber);
      },
      onShowCalendarUpdate: () => {
        LocalStorage.setShowCalendar(showCalendar);
      },
    }
  }

  useLayoutEffect(events.fetch, [fetchFrom, params.root, search]);
  useLayoutEffect(events.onReminderNumberUpdate, [reminderNumber]);
  useLayoutEffect(events.onShowCalendarUpdate, [showCalendar]);

  return (
    <Page sidebar={{root: root, onSearch: setSearch, reminderNumber: reminderNumber}}>
      <TaskPanelHeading title={title} icon={icon} root={root} events={events}/>
      {showCalendar ? <TaskReminderCalendar tasks={tasks} statuses={statuses} events={events}/> : null}
      <PanelBody>
        <LazyLoading loadMore={events.loadMore} hasMore={startFrom != null}>
          <TaskList data={{
            root: root,
            tasks: tasks,
            statuses: statuses
          }} events={events}/>
        </LazyLoading>
      </PanelBody>
    </Page>
  );
}

export default TasksPage;
